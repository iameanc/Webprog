<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
verify_csrf();
$itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
$proof = trim((string)($_POST['proof'] ?? ''));
if (!$itemId || $proof === '' || strlen($proof) > 1000) {
    flash('error', 'Add a short description that helps verify your claim.');
    redirect('index.php');
}
$statement = db()->prepare("SELECT id, user_id FROM items WHERE id = ? AND item_type = 'found' AND status = 'open'");
$statement->execute([$itemId]);
$item = $statement->fetch();
if (!$item || (int)$item['user_id'] === (int)current_user()['id']) {
    flash('error', 'This listing is not available to claim.');
    redirect('index.php');
}
try {
    $statement = db()->prepare('INSERT INTO claims (item_id, user_id, proof) VALUES (?, ?, ?)');
    $statement->execute([$itemId, current_user()['id'], $proof]);
    flash('success', 'Your claim was sent to the campus admin for review.');
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        flash('error', 'You have already submitted a claim for this item.');
    } else {
        throw $exception;
    }
}
redirect('index.php');
