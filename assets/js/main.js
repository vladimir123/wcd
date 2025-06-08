$(document).ready(function () {
    const citySelect = document.querySelector('#citySelect');
    const choices = new Choices(citySelect, {
        searchEnabled: true,
        itemSelectText: '',
        shouldSort: false,
        fuseOptions: {
        keys: ['label', 'customProperties.country']
        },
        callbackOnCreateTemplates: function (template) {
        return {
            option: (classNames, data) => {
            return template(`
                <div class="${classNames.item} ${classNames.itemChoice}" data-select-text="" data-choice
                    data-id="${data.id}" data-value="${data.value}" ${data.groupId > 0 ? 'role="treeitem"' : 'role="option"'}
                    ${data.disabled ? 'aria-disabled="true"' : ''}>
                ${data.label}
                </div>
            `);
            }
        };
        }
    });

const items = citySelect.querySelectorAll('option');
items.forEach((option, index) => {
    const country = option.getAttribute('data-country');
    if (country) {
    choices._store.choices[index].customProperties = { country: country };
    }
});

  function loadWeather(city) {
    console.log("city => ", city);
    if (!city) {
      $('#weatherResult').text('Please select a city.');
      return;
    }
    $.getJSON('api/weather.php', { city }, function (data) {
      $('#weatherResult').html(`
        <strong>${data.city}</strong><br>
        Temperature: ${data.temp}°C<br>
        Condition: ${data.condition}
      `);
    }).fail(function () {
      $('#weatherResult').text('Failed to load weather data.');
    });
  }

  function loadCurrency(cntry) {
    if (!cntry) {
      $('#currencyResult').text('Please select country.');
      return;
    }
    $.ajax({
      type: "POST",
      url: 'api/currency.php',
      data: {country: cntry},
      dataType: "json",
      success: function (data) {
        $('#currencyResult').html(`1 USD = ${data.rateToUSD.toFixed(2)} ${data.currency}`);
      },
      error: function (xhr) {
        $('#currencyResult').html(`Error fetching currency data: ${xhr.responseJSON.error}`);
      }
    });
  }

  $('#citySelect').on('change', function () {
    loadWeather(this.value);
  });

  $('#countrySelect').on('change', function () {
    let country = this.options[this.selectedIndex].text;
    loadCurrency(country);
    $('#currencyResult').html("");
    $('#weatherResult').text('Please select a city.');
  });

  loadWeather();
  loadCurrency();

});
