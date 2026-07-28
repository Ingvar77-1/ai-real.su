<?php
header('Content-Type: application/json');

$token = $_GET['token'] ?? '';
$file = $_GET['file'] ?? '';
$secret = trim(file_get_contents(__DIR__ . '/.token'));

if ($token !== $secret) { http_response_code(401); exit('Unauthorized'); }

// Защита от path traversal
if (!$file || strpos($file, '/') !== false || strpos($file, '..') !== false) {
    http_response_code(400); exit('Invalid filename');
}

$path = __DIR__ . '/new/' . $file;
if (!file_exists($path)) { http_response_code(404); exit('File not found'); }

readfile($path);
