<?php
require_once '../config.php';


$url = EXCHANGE_RATE_API_URL . "?base=USD&symbols=EUR,GBP,JPY";

$response = file_get_contents($url);
$data = json_decode($response, true);

if ($data && isset($data['rates'])) {
    echo json_encode($data['rates']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch exchange rates']);
}
