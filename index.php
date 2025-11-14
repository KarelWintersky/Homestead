<?php

use Homestead\Homestead;
use Symfony\Component\Yaml\Exception\ParseException;

if (!defined("START_TIME")) { define("START_TIME", microtime(true)); }
if (!defined("CONFIG_PATH")) { define("CONFIG_PATH",
    $_SERVER['APP_CONFIG'] ?? __DIR__ . DIRECTORY_SEPARATOR . 'config'
); };

require_once __DIR__ . '/vendor/autoload.php';

try {
    Homestead::init(CONFIG_PATH);
    Homestead::loadCredentials();

    $layoutConfig = Homestead::loadLayoutConfig();
    $layoutStyles = $layoutConfig['layout'] ?? [];
    $favicon = $layoutStyles['favicon'] ?? null;
    $ogConfig = $layoutConfig['opengraph'] ?? [];

    // Загружаем все конфиги секций
    $allSectionsConfig = Homestead::loadSectionsConfig();
    $sections = Homestead::loadSections($allSectionsConfig);

    require_once __DIR__ . '/templates/main.php';

} catch (RuntimeException|ParseException|RedisException $e) {
    die($e->getMessage());
}


if (defined("START_TIME")) {
    $executionTime = round((microtime(true) - START_TIME) * 1000, 2);
    $memoryUsage = round(memory_get_usage(true) / 1024 , 2);
    echo "<!-- Generated in {$executionTime} ms, Memory: {$memoryUsage} Kb -->";
}

die;