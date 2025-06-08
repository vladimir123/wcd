<?php
include_once '../config.php';
header('Content-Type: application/json');

$country = isset($_POST['country']) ? trim($_POST['country']) : '';
if ($country === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing country name']);
    exit;
}

// get country currency
$currencyInfo = getCurrencyByCountry($country);
if (!$currencyInfo["success"]) {
    http_response_code(500);
    echo json_encode(['error' => $currencyInfo['error'] ]);
    exit;
}

$currencyCode = $currencyInfo['data']['currency'];
// currency to USD exchange rate
$rate = getExchangeRate($currencyCode);
if(!$rate || empty($rate))
{
    http_response_code(500);
    echo json_encode(['error' => $rate["error"]]);
    exit;
}

echo json_encode([
    'country' => $country,
    'currency' => $currencyCode,
    'rateToUSD' => $rate
]);

function getCurrencyByCountry(string $country): array {
    $url = COUNTRY_CURRENCY_API;
    $postData = ['country' => $country];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\nAccept: application/json\r\n",
            'content' => json_encode($postData),
            'ignore_errors' => true
        ]
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if (!$result) {
        return [
            'success' => false,
            'error' => 'No response from currency API'
        ];
    }

    $parsed = json_decode($result, true);

    if (!empty($parsed['error'])) {
        return [
            'success' => false,
            'error' => $parsed['msg'] ?? 'Unknown API error'
        ];
    }

    if (!isset($parsed['data']['currency'])) {
        return [
            'success' => false,
            'error' => 'Currency not found in API response'
        ];
    }

    return [
        'success' => true,
        'data' => [
            'country' => $parsed['data']['name'],
            'currency' => $parsed['data']['currency'],
            'iso2' => $parsed['data']['iso2'] ?? null,
            'iso3' => $parsed['data']['iso3'] ?? null
        ]
    ];
}



function getExchangeRate(string $currencyCode="EUR"): float|false {
    $url = EXCHANGE_RATE_API."/USD/{$currencyCode}";
    $response = @file_get_contents($url);

    if (!$response) return false;

    $parsed = json_decode($response, true);

    return $parsed['conversion_rate'] ?? false;
}
?>