<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <?php
        require_once 'config.php';
  ?>
  <title>Weather & Currency Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/choices.js@11.1.0/public/assets/scripts/choices.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/choices.js@11.1.0/public/assets/styles/choices.min.css" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
  <div class="container">
    <h2 class="mb-4">Weather & Currency Dashboard</h2>

    <?php
        $response = file_get_contents(ALLCOUNTRIES);
        $data = json_decode($response);
        $cleanData = isset($data->data) ? $data->data : $data;
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

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
  <script src="assets/js/country_city.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

</body>
</html>
