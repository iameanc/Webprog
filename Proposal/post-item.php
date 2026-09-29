<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
require_login();

$categories = db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$error = '';
$values = ['item_type' => 'lost', 'title' => '', 'category_id' => '', 'location' => '', 'item_date' => date('Y-m-d'), 'description' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($values as $key => $_) {
        $values[$key] = trim((string)($_POST[$key] ?? ''));
    }
    $date = DateTime::createFromFormat('!Y-m-d', $values['item_date']);
    $dateValid = $date && $date->format('Y-m-d') === $values['item_date'] && $date <= new DateTime('today');
    $categoryIds = array_map(static fn(array $category): string => (string)$category['id'], $categories);
    if (!in_array($values['item_type'], ['lost', 'found'], true) || $values['title'] === '' || strlen($values['title']) > 140 || !in_array($values['category_id'], $categoryIds, true) || $values['location'] === '' || strlen($values['location']) > 180 || !$dateValid || $values['description'] === '' || strlen($values['description']) > 12000) {
        $error = 'Complete each field with a valid item date and category.';
    } elseif (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Choose a photo to upload (maximum 5 MB).';
    } elseif ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
        $error = 'The photo must be 5 MB or smaller.';
    } else {
        $temporaryFile = $_FILES['photo']['tmp_name'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($temporaryFile);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        if (!isset($extensions[$mime]) || getimagesize($temporaryFile) === false) {
            $error = 'Upload a valid JPG, PNG, WEBP, or GIF image.';
        } else {
            $uploadDirectory = __DIR__ . '/uploads';
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }
            $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
            if (!move_uploaded_file($temporaryFile, $uploadDirectory . '/' . $filename)) {
                $error = 'The photo could not be saved. Check upload folder permissions.';
            } else {
                $photoPath = 'uploads/' . $filename;
                $statement = db()->prepare('INSERT INTO items (user_id, category_id, item_type, title, description, location, item_date, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $statement->execute([current_user()['id'], $values['category_id'], $values['item_type'], $values['title'], $values['description'], $values['location'], $values['item_date'], $photoPath]);
                flash('success', 'Your item listing is now live.');
                redirect('index.php');
            }
        }
    }
}
render_header('Post an item');
?>
<section class="form-page"><div class="form-heading"><p class="eyebrow">ADD A LISTING</p><h1>Help it find its way.</h1><p>Share enough detail for someone to recognize the item. Keep unique identifying details for claim verification.</p></div>
<div class="form-panel form-panel-wide">
<?php if ($error): ?><div class="notice notice-error"><?= e($error) ?></div><?php endif; ?>
<form class="stack-form" method="post" action="post-item.php" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-row"><label>Listing type<select name="item_type" required><option value="lost" <?= $values['item_type'] === 'lost' ? 'selected' : '' ?>>I lost this</option><option value="found" <?= $values['item_type'] === 'found' ? 'selected' : '' ?>>I found this</option></select></label>
    <label>Category<select name="category_id" required><option value="">Choose a category</option><?php foreach ($categories as $category): ?><option value="<?= (int)$category['id'] ?>" <?= $values['category_id'] === (string)$category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?></select></label></div>
    <label>Item name<input name="title" maxlength="140" value="<?= e($values['title']) ?>" placeholder="e.g. Blue water bottle" required></label>
    <div class="form-row"><label>Campus location<input name="location" maxlength="180" value="<?= e($values['location']) ?>" placeholder="e.g. Library, second floor" required></label><label>Date lost or found<input name="item_date" type="date" max="<?= e(date('Y-m-d')) ?>" value="<?= e($values['item_date']) ?>" required></label></div>
    <label>Description<textarea name="description" rows="4" maxlength="3000" required placeholder="Color, brand, general appearance..."><?= e($values['description']) ?></textarea></label>
    <label>Photo<input id="photo-input" name="photo" type="file" accept="image/jpeg,image/png,image/webp,image/gif" required><small>JPG, PNG, WEBP, or GIF. Up to 5 MB.</small></label>
    <img id="photo-preview" class="photo-preview" alt="Selected item photo preview" hidden>
    <button class="button" type="submit">Publish listing</button>
</form></div></section>
<?php render_footer(); ?>
