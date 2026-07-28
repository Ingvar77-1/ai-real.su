<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed'); }

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['name']) || !isset($input['contact'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверные данные']);
    exit();
}

// Уникальное имя файла
$filename = 'lead_' . time() . '_' . bin2hex(random_bytes(4)) . '.json';

$lead = [
    'id' => $filename,
    'timestamp' => date('c'),
    'source' => 'ai-real.su',
    'name' => htmlspecialchars($input['name']),
    'company' => htmlspecialchars($input['company'] ?? 'Не указана'),
    'contact' => htmlspecialchars($input['contact']),
    'message' => htmlspecialchars($input['message'] ?? 'Без сообщения'),
    'status' => 'pending'
];

$dir = __DIR__ . '/new';
if (!is_dir($dir)) { mkdir($dir, 0755, true); }

// Атомарная запись через временный файл
$tmpFile = tempnam($dir, 'tmp_');
file_put_contents($tmpFile, json_encode($lead, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
rename($tmpFile, $dir . '/' . $filename);

echo json_encode(['success' => true, 'id' => $filename]);
