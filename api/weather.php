<?php
require_once '../config.php';

$city = $_GET['city'] ?? 'London';
$url = WEATHER_API_URL . "?q=".urlencode($city)."&appid=". OPENWEATHER_API_KEY."&units=metric";

$response = file_get_contents($url);
$data = json_decode($response, true);

if ($data && isset($data['main'])) {
    echo json_encode([
        'city' => $data['name'],
        'temp' => $data['main']['temp'],
        'condition' => $data['weather'][0]['description']
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch weather']);
}
