<?php
header('Content-Type: application/json');

$token = $_GET['token'] ?? '';
$file = $_GET['file'] ?? '';
$secret = trim(file_get_contents(__DIR__ . '/.token'));

if ($token !== $secret) { http_response_code(401); exit('Unauthorized'); }

if (!$file || strpos($file, '/') !== false || strpos($file, '..') !== false) {
    http_response_code(400); exit('Invalid filename');
}

$newPath = __DIR__ . '/new/' . $file;
$archivePath = __DIR__ . '/archive/' . $file;

if (!file_exists($newPath)) { http_response_code(404); exit('File not found'); }

if (rename($newPath, $archivePath)) {
    echo json_encode(['success' => true, 'archived' => $file]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to move file']);
}
