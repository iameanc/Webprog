<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    try {
        if ($action === 'claim_approve' && $id) {
            $pdo = db();
            $pdo->beginTransaction();
            $statement = $pdo->prepare("SELECT c.id, c.item_id FROM claims c JOIN items i ON i.id = c.item_id WHERE c.id = ? AND c.status = 'pending' AND i.status = 'open' FOR UPDATE");
            $statement->execute([$id]);
            $claim = $statement->fetch();
            if (!$claim) {
                throw new RuntimeException('That claim is no longer pending.');
            }
            $pdo->prepare("UPDATE claims SET status = 'approved' WHERE id = ?")->execute([$id]);
            $pdo->prepare("UPDATE items SET status = 'returned' WHERE id = ?")->execute([$claim['item_id']]);
            $pdo->prepare("UPDATE claims SET status = 'rejected' WHERE item_id = ? AND id <> ? AND status = 'pending'")->execute([$claim['item_id'], $id]);
            $pdo->commit();
            flash('success', 'Claim approved and item marked returned.');
        } elseif ($action === 'claim_reject' && $id) {
            db()->prepare("UPDATE claims SET status = 'rejected' WHERE id = ? AND status = 'pending'")->execute([$id]);
            flash('success', 'Claim rejected.');
        } elseif ($action === 'return_item' && $id) {
            db()->prepare("UPDATE items SET status = 'returned' WHERE id = ?")->execute([$id]);
            flash('success', 'Item marked returned.');
        } elseif ($action === 'delete_item' && $id) {
            db()->prepare('DELETE FROM items WHERE id = ?')->execute([$id]);
            flash('success', 'Listing removed.');
        } elseif ($action === 'user_role' && $id && $id !== (int)current_user()['id']) {
            $role = (string)($_POST['role'] ?? 'user');
            if (in_array($role, ['user', 'admin'], true)) {
                db()->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$role, $id]);
                flash('success', 'User role updated.');
            }
        } else {
            flash('error', 'That admin action could not be completed.');
        }
    } catch (Throwable $exception) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        flash('error', $exception instanceof RuntimeException ? $exception->getMessage() : 'The action could not be completed.');
    }
    redirect('admin.php');
}

$pdo = db();
$stats = [
    'open' => (int)$pdo->query("SELECT COUNT(*) FROM items WHERE status = 'open'")->fetchColumn(),
    'returned' => (int)$pdo->query("SELECT COUNT(*) FROM items WHERE status = 'returned'")->fetchColumn(),
    'pending' => (int)$pdo->query("SELECT COUNT(*) FROM claims WHERE status = 'pending'")->fetchColumn(),
    'users' => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
];
$claims = $pdo->query("SELECT c.id, c.proof, c.created_at, u.name AS claimant, u.email, i.id AS item_id, i.title AS item_title, i.photo_path FROM claims c JOIN users u ON u.id = c.user_id JOIN items i ON i.id = c.item_id WHERE c.status = 'pending' ORDER BY c.created_at ASC LIMIT 100")->fetchAll();
$items = $pdo->query('SELECT i.id, i.title, i.item_type, i.status, i.created_at, u.name AS owner FROM items i JOIN users u ON u.id = i.user_id ORDER BY i.created_at DESC LIMIT 50')->fetchAll();
$users = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 100')->fetchAll();
render_header('Admin dashboard');
?>
<section class="admin-page"><div class="section-heading admin-heading"><div><p class="eyebrow">CAMPUS OFFICE</p><h1>Admin dashboard</h1></div></div>
<div class="stats-grid"><div class="stat"><span>Open listings</span><strong><?= $stats['open'] ?></strong></div><div class="stat"><span>Returned</span><strong><?= $stats['returned'] ?></strong></div><div class="stat"><span>Claims to review</span><strong><?= $stats['pending'] ?></strong></div><div class="stat"><span>Registered users</span><strong><?= $stats['users'] ?></strong></div></div>
<section class="admin-section"><div class="section-heading"><div><p class="eyebrow">REVIEW QUEUE</p><h2>Pending claims</h2></div></div>
<?php if (!$claims): ?><p class="empty-state">Nothing waiting for review.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Item</th><th>Claimant</th><th>Proof submitted</th><th>Received</th><th>Decision</th></tr></thead><tbody>
<?php foreach ($claims as $claim): ?><tr><td><strong><?= e($claim['item_title']) ?></strong><small>Listing #<?= (int)$claim['item_id'] ?></small></td><td><?= e($claim['claimant']) ?><small><?= e($claim['email']) ?></small></td><td class="proof-cell"><?= e($claim['proof']) ?></td><td><?= e(date('M j, Y', strtotime($claim['created_at']))) ?></td><td><div class="action-stack"><form method="post" action="admin.php"><?= csrf_field() ?><input type="hidden" name="action" value="claim_approve"><input type="hidden" name="id" value="<?= (int)$claim['id'] ?>"><button class="button button-small" type="submit">Approve</button></form><form method="post" action="admin.php"><?= csrf_field() ?><input type="hidden" name="action" value="claim_reject"><input type="hidden" name="id" value="<?= (int)$claim['id'] ?>"><button class="button button-small button-quiet" type="submit">Reject</button></form></div></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section>
<section class="admin-section"><div class="section-heading"><div><p class="eyebrow">LISTING MANAGEMENT</p><h2>Recent items</h2></div></div><div class="table-wrap"><table><thead><tr><th>Item</th><th>Posted by</th><th>Type / status</th><th>Action</th></tr></thead><tbody>
<?php foreach ($items as $item): ?><tr><td><strong><?= e($item['title']) ?></strong><small><?= e(date('M j, Y', strtotime($item['created_at']))) ?></small></td><td><?= e($item['owner']) ?></td><td><?= e(ucfirst($item['item_type'])) ?> · <?= e(ucfirst($item['status'])) ?></td><td><div class="action-stack"><?php if ($item['status'] === 'open'): ?><form method="post" action="admin.php"><?= csrf_field() ?><input type="hidden" name="action" value="return_item"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="text-button" type="submit">Mark returned</button></form><?php endif; ?><form method="post" action="admin.php" data-confirm="Remove this listing and its claims?"><?= csrf_field() ?><input type="hidden" name="action" value="delete_item"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="text-button text-danger" type="submit">Delete</button></form></div></td></tr><?php endforeach; ?>
<?php if (!$items): ?><tr><td colspan="4">No listings yet.</td></tr><?php endif; ?></tbody></table></div></section>
<section class="admin-section"><div class="section-heading"><div><p class="eyebrow">ACCOUNT ACCESS</p><h2>Users</h2></div></div><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Change role</th></tr></thead><tbody>
<?php foreach ($users as $user): ?><tr><td><?= e($user['name']) ?><?php if ((int)$user['id'] === (int)current_user()['id']): ?><small>You</small><?php endif; ?></td><td><?= e($user['email']) ?></td><td><span class="role-label"><?= e(ucfirst($user['role'])) ?></span></td><td><?php if ((int)$user['id'] !== (int)current_user()['id']): ?><form class="role-form" method="post" action="admin.php"><?= csrf_field() ?><input type="hidden" name="action" value="user_role"><input type="hidden" name="id" value="<?= (int)$user['id'] ?>"><select name="role" aria-label="Role for <?= e($user['name']) ?>"><option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option><option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option></select><button class="text-button" type="submit">Save</button></form><?php else: ?><span class="muted">Current account</span><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></section>
</section>
<?php render_footer(); ?>
