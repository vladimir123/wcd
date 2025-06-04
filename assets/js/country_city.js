document.addEventListener('DOMContentLoaded', function () {
  const rawData = window.countryCityData || [];

  const countrySelect = document.getElementById('countrySelect');
  const citySelect = document.getElementById('citySelect');

  const countryChoices = new Choices(countrySelect, {
    searchEnabled: true,
    itemSelectText: '',
  });

  const cityChoices = new Choices(citySelect, {
    searchEnabled: true,
    itemSelectText: '',
  });

countrySelect.addEventListener('change', function () {
  const selectedIso2 = this.value;
  const selectedCountry = rawData.find(c => c.iso2 === selectedIso2);

  cityChoices.clearStore();
  cityChoices.disable();
  citySelect.setAttribute('disabled', 'disabled');

  if (selectedCountry && Array.isArray(selectedCountry.cities)) {
    const cities = selectedCountry.cities.map(city => ({
      value: city,
      label: city
    }));

    cityChoices.setChoices(cities, 'value', 'label', true);

    citySelect.removeAttribute('disabled');
    cityChoices.enable();
    cityChoices.setChoiceByValue('');
  }
});
});