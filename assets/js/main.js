$(document).ready(function () {
    const citySelect = document.querySelector('#citySelect');
    const choices = new Choices(citySelect, {
        searchEnabled: true,
        itemSelectText: '',
        shouldSort: false,
        fuseOptions: {
        // Включаем поиск по `label` и `customProperties.country`
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

// Добавляем customProperties для поиска
const items = citySelect.querySelectorAll('option');
items.forEach((option, index) => {
    const country = option.getAttribute('data-country');
    if (country) {
    choices._store.choices[index].customProperties = { country: country };
    }
});

  function loadWeather(city) {
    if (!city) {
      $('#weatherResult').text('Please select a city.');
      return;
    }
    $('#weatherResult').text('Loading...');
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

  function loadCurrency() {
    $('#currencyResult').text('Loading...');
    $.getJSON('api/currency.php', function (rates) {
      let html = '<ul>';
      for (const [key, value] of Object.entries(rates)) {
        html += `<li>USD → ${key}: ${value.toFixed(2)}</li>`;
      }
      html += '</ul>';
      $('#currencyResult').html(html);
    }).fail(function () {
      $('#currencyResult').text('Failed to load exchange rates.');
    });
  }

  $('#citySelect').on('change', function () {
    loadWeather(this.value);
  });

loadWeather();
loadCurrency();

});
