<?php
function currency_plugin($options) {
    $redis = \Homestead\Homestead::$redis;

    $cacheKey = 'currency:' . md5(implode(',', $options['currencies']));
    $cacheTtl = $options['cache_ttl'] ?? 3600;

    // Пытаемся получить данные из кеша
    if ($redis && $cached = $redis->get($cacheKey)) {
        return $cached;
    }

    // Получаем свежие данные с ЦБ РФ
    $api_url = 'http://www.cbr.ru/scripts/XML_daily.asp';
    $response = @file_get_contents($api_url);

    if (!$response) {
        return [
            'error' => 'Failed to fetch currency data',
            'html' => '<div class="currency-error">Currency service unavailable</div>'
        ];
    }

    // Парсим XML
    $xml = simplexml_load_string($response);
    if (!$xml) {
        return [
            'error' => 'Invalid currency data format',
            'html' => '<div class="currency-error">Invalid data format</div>'
        ];
    }

    $currencies = [];
    foreach ($xml->Valute as $valute) {
        $code = (string)$valute['ID'];
        if (in_array($code, $options['currencies'])) {
            $currencies[$code] = [
                'name' => (string)$valute->Name,
                'value' => (float)str_replace(',', '.', $valute->Value),
                'nominal' => (int)$valute->Nominal,
                'code' => (string)$valute->CharCode
            ];
        }
    }

    // Формируем HTML
    $html = '<div class="currency-widget"><h3>Exchange Rates</h3><ul>';

    foreach ($currencies as $currency) {
        $rate = $currency['value'] / $currency['nominal'];
        $html .= sprintf(
            '<li>%s: <strong>%.2f ₽</strong> <span class="currency-meta">(for %d %s)</span></li>',
            $currency['name'],
            $rate,
            $currency['nominal'],
            $currency['code']
        );
    }

    $html .= '</ul><div class="cache-info">Updated: ' . date('H:i') . '</div></div>';

    $result = [
        'css' => file_exists(__DIR__.'/currency.css') ? file_get_contents(__DIR__.'/currency.css') : '',
        'html' => $html,
        'data' => $currencies,
        '_cached' => time()
    ];

    // Сохраняем в кеш
    if ($redis) {
        $redis->set($cacheKey, $result, $cacheTtl);
    }

    return $result;
}

return 'currency_plugin';