<?php

use Homestead\Homestead;
use Homestead\HomesteadException;
use Symfony\Component\Yaml\Exception\ParseException;
if (!defined('HOMESTEAD_VERSION')) { define('HOMESTEAD_VERSION', '%HOMESTEAD_VERSION%'); }
if (!defined("START_TIME")) { define("START_TIME", microtime(true)); }
if (!defined("CONFIG_PATH")) { define("CONFIG_PATH",
    $_SERVER['APP_CONFIG'] ?? __DIR__ . DIRECTORY_SEPARATOR . 'config'
); };
if (!defined("IS_PRODUCTION")) { define("IS_PRODUCTION", !is_file(__DIR__ . DIRECTORY_SEPARATOR . 'composer.lock')); }

if (IS_PRODUCTION === false) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/homestead.phar';
}

try {
    Homestead::init(CONFIG_PATH);
    Homestead::loadCredentials();

    $layoutConfig = Homestead::loadLayoutConfig();

    $layoutData = $layoutConfig['layout'] ?? [];
    $layoutStyles = $layoutConfig['styles'] ?? [];

    $favicon = $layoutData['favicon'] ?? '';
    $ogConfig = $layoutConfig['opengraph'] ?? [];

    // Загружаем все конфиги секций
    $allSectionsConfig = Homestead::loadSectionsConfig();
    $sections = Homestead::loadSections($allSectionsConfig);

} catch (HomesteadException $e) {
    $errorMessage = $e->getMessage();
} catch (RuntimeException|ParseException|RedisException $e) {
    die($e->getMessage());
} finally {
    $currentYear = date('Y');
    $copyrightYear = ($currentYear == 2025) ? '2025' : "2025&mdash;$currentYear";

    $template = \Homestead\Template::render_template("templates/main.php", [
        'layoutConfig'  =>  $layoutConfig ?? [],

        'layoutStyles'  =>  $layoutStyles ?? [],
        'layoutData'    =>  $layoutData ?? [],
        'ogConfig'      =>  $ogConfig ?? [],
        'favicon'       =>  $favicon ?? [],

        'sections'      =>  $sections ?? [],

        'errorMessage'  =>  $errorMessage ?? '',
        'copyrightYear' =>  $copyrightYear
    ]);

    echo $template;
}

if (defined("START_TIME")) {
    $executionTime = round((microtime(true) - START_TIME) * 1000, 2);
    $memoryUsage = round(memory_get_usage(true) / 1024 , 2);
    // $isPhar = defined('__COMPILER_HALT_OFFSET__') || str_starts_with(__FILE__, 'phar://');
    // $isPhar = $isPhar || IS_PRODUCTION ? 'PHAR' : 'PHP';
    $mode = IS_PRODUCTION ? 'production' : 'dev';
    $configPath = CONFIG_PATH;
    echo "<!-- Generated in {$executionTime} ms, memory consumed: {$memoryUsage} Kb, config at: {$configPath}, mode: {$mode} -->";
}

die;