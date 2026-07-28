<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$BOT_TOKEN = '8472803704:AAFc6qF-X0BlQIGyOqQk_OpbRJu7oqEBxHo';
$CHAT_ID = '184288832';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['name']) || !isset($input['contact'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверные данные']);
    exit();
}

$name = htmlspecialchars($input['name']);
$company = isset($input['company']) && $input['company'] ? htmlspecialchars($input['company']) : 'Не указана';
$contact = htmlspecialchars($input['contact']);
$message = isset($input['message']) && $input['message'] ? htmlspecialchars($input['message']) : 'Без сообщения';

$text = "🔥 <b>lead from ai-real.su</b>\n\n";
$text .= "👤 <b>Имя:</b> {$name}\n";
$text .= "🏢 <b>Компания:</b> {$company}\n";
$text .= "📞 <b>Контакт:</b> {$contact}\n";
$text .= "📝 <b>Задача:</b> {$message}\n\n";
$text .= "⏳ Ожидает обработки агентом OpenClaw...";

$url = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";
$data = [
    'chat_id' => $CHAT_ID,
    'text' => $text,
    'parse_mode' => 'HTML'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка Telegram API', 'details' => $response]);
}
