<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$conditions = [];
$params = [];
$query = trim((string)($_GET['q'] ?? ''));
if ($query !== '') {
    $conditions[] = '(i.title LIKE :title OR i.description LIKE :description OR i.location LIKE :location)';
    $term = '%' . $query . '%';
    $params['title'] = $term;
    $params['description'] = $term;
    $params['location'] = $term;
}
foreach (['category' => 'i.category_id', 'type' => 'i.item_type', 'status' => 'i.status'] as $key => $column) {
    $value = trim((string)($_GET[$key] ?? ''));
    if ($value !== '' && $value !== '0') {
        $conditions[] = $column . ' = :' . $key;
        $params[$key] = $value;
    }
}
$dateFrom = (string)($_GET['date_from'] ?? '');
$dateTo = (string)($_GET['date_to'] ?? '');
if ($dateFrom !== '') {
    $conditions[] = 'i.item_date >= :date_from';
    $params['date_from'] = $dateFrom;
}
if ($dateTo !== '') {
    $conditions[] = 'i.item_date <= :date_to';
    $params['date_to'] = $dateTo;
}
$sql = 'SELECT i.id, i.user_id, i.item_type, i.title, i.description, i.location, i.item_date, i.photo_path, i.status, i.created_at, c.name AS category_name FROM items i JOIN categories c ON c.id = i.category_id';
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY i.created_at DESC LIMIT 100';
$statement = db()->prepare($sql);
$statement->execute($params);
$items = $statement->fetchAll();
echo json_encode(['items' => $items, 'count' => count($items)], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
