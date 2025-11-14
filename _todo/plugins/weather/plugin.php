<?php

function weather_plugin($options) {
    $redis = \Homestead\Homestead::$redis;

    $cacheKey = "weather:{$options['city']}:{$options['units']}";
    $cacheTtl = $options['cache_ttl'] ?? 1800;

    // Пытаемся получить данные из кеша
    if ($redis && $cached = $redis->get($cacheKey)) {
        return $cached;
    }

    // Получаем свежие данные
    $api_url = sprintf(
        "https://api.openweathermap.org/data/2.5/weather?q=%s&units=%s&appid=%s",
        urlencode($options['city']),
        $options['units'],
        $options['api_key']
    );

    $response = @file_get_contents($api_url);
    if (!$response) {
        return [
            'error' => 'Failed to fetch weather data',
            'html' => '<div class="weather-error">Weather service unavailable</div>'
        ];
    }

    $data = json_decode($response, true);

    // Формируем HTML
    $html = sprintf('
        <div class="weather-widget">
            <h3>Weather in %s</h3>
            <div class="weather-main">
                <img src="https://openweathermap.org/img/wn/%s@2x.png" alt="Weather icon">
                <span class="temp">%d°C</span>
            </div>
            <div class="weather-details">
                <span>Humidity: %d%%</span>
                <span>Wind: %d m/s</span>
            </div>
        </div>',
        $data['name'],
        $data['weather'][0]['icon'],
        round($data['main']['temp']),
        $data['main']['humidity'],
        $data['wind']['speed']
    );

    $result = [
        'css' => file_exists(__DIR__ . '/weather.css') ? file_get_contents(__DIR__ . '/weather.css') : '',
        'js' => file_exists(__DIR__ . '/weather.js') ? file_get_contents(__DIR__ . '/weather.js') : '',
        'html' => $html,
        'data' => $data,
        '_cached' => time()
    ];

    // Сохраняем в кеш
    if ($redis) {
        $redis->set($cacheKey, $result, $cacheTtl);
    }

    return $result;
}

return 'weather_plugin';