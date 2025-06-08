<?php error_reporting(0); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <?php
        require_once 'config.php';
  ?>
  <title>Weather & Currency Dashboard</title>
  <script src="assets/js/libs/choices.min.js"></script>
  <script src="assets/js/libs/jquery-3.7.1.min.js"></script>
  <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
  <script src="assets/js/country_city.js"></script>


  <link href="assets/css/libs/choices.min.css" rel="stylesheet">
  <link href="assets/css/libs/bootstrap/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/libs/fontawesome/all.css">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
  <div class="container">
    <h2 class="mb-4">Weather & Currency Dashboard</h2>

    <?php
      function fetchWithRetries($url, $maxRetries = 5, $initialDelay = 1) {
          $attempt = 0;
          $delay = $initialDelay;

          while ($attempt < $maxRetries) {
              $opts = [
                  "http" => [
                      "method" => "GET",
                      "header" => "Accept: application/json\r\n"
                  ]
              ];
              $context = stream_context_create($opts);
              $response = @file_get_contents($url, false, $context);

              $httpCode = 0;
              if (isset($http_response_header)) {
                  if (preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $http_response_header[0], $matches)) {
                      $httpCode = intval($matches[1]);
                  }
              }

              if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
                  return $response;
              }

              if ($httpCode == 429 || $httpCode == 503) {
                  // timeout
                  sleep($delay);
                  $delay *= 2; // increse delay for next attempt
              } else {
                  break;
              }

              $attempt++;
          }

          return false;
      }

      try {
          $response = fetchWithRetries(ALLCOUNTRIES);

          if ($response === false) {
              throw new Exception("API timeout. Please try again later");
          }

          $data = json_decode($response);
          if (json_last_error() !== JSON_ERROR_NONE) {
              throw new Exception("JSON error: " . json_last_error_msg());
          }

          $cleanData = isset($data->data) ? $data->data : $data;

          // Используй $cleanData дальше
      } catch (Exception $e) {
          echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . " <i class='fa-solid fa-arrows-rotate' style='cursor: pointer;' onclick='location.reload();' title='Reload'></i></div>";
      }
  ?>


    <div class="mb-3">
        <label for="countrySelect" class="form-label">Country:</label>
        <select id="countrySelect" class="form-select">
            <option value="" selected disabled>Select country</option>
            <?php foreach ($cleanData as $country): ?>
                <option value="<?= htmlspecialchars($country->iso2) ?>"><?= htmlspecialchars($country->country) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="citySelect" class="form-label">City:</label>
        <select id="citySelect" class="form-select" disabled>
            <option value="" selected>Select city</option>
        </select>
    </div>
    <div id="weatherSection" class="mb-4">
      <h4>Weather</h4>
      <div id="weatherResult" class="border p-3 bg-white">Loading...</div>
    </div>

    <div id="currencySection">
      <h4>Currency Exchange</h4>
      <div id="currencyResult" class="border p-3 bg-white">Loading...</div>
    </div>
  </div>

<script>
  window.countryCityData = <?php echo json_encode($cleanData); ?>;
</script>


</body>
</html>
