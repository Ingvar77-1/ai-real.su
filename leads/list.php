<?php
header('Content-Type: application/json');

$token = $_GET['token'] ?? '';
$secret = trim(file_get_contents(__DIR__ . '/.token'));
if ($token !== $secret) { http_response_code(401); exit('Unauthorized'); }

$files = glob(__DIR__ . '/new/*.json');
$result = [];
foreach ($files as $file) {
    $result[] = basename($file);
}
// Сортируем по времени создания (старые первые)
sort($result);

echo json_encode(['count' => count($result), 'files' => $result]);
