<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

verify_csrf();
$itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
if (!$itemId) {
    flash('error', 'That listing could not be found.');
    redirect('index.php');
}

$statement = db()->prepare('SELECT photo_path FROM items WHERE id = ? AND user_id = ?');
$statement->execute([$itemId, current_user()['id']]);
$item = $statement->fetch();
if (!$item) {
    flash('error', 'You can only delete your own listings.');
    redirect('index.php');
}

db()->prepare('DELETE FROM items WHERE id = ? AND user_id = ?')->execute([$itemId, current_user()['id']]);

$photoPath = (string)$item['photo_path'];
if (str_starts_with($photoPath, 'uploads/')) {
    $photoFile = __DIR__ . '/' . $photoPath;
    if (is_file($photoFile)) {
        unlink($photoFile);
    }
}

flash('success', 'Your listing was deleted.');
redirect('index.php');
