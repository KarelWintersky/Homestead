<?php

use Arris\Toolkit\RedisClientException;
use Homestead\Homestead;
use Symfony\Component\Yaml\Exception\ParseException;

if (!defined("START_TIME")) { define("START_TIME", microtime(true)); }
if (!defined("CONFIG_PATH")) { define("CONFIG_PATH", __DIR__ . DIRECTORY_SEPARATOR . 'config'); };
if (!defined("INSTALL_PATH")) { define("INSTALL_PATH", __DIR__); }
if (!defined("IS_PRODUCTION")) { define("IS_PRODUCTION", !is_file(__DIR__ . DIRECTORY_SEPARATOR . 'composer.lock')); }

if (IS_PRODUCTION === false) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/homestead.phar';
}

try {
    Homestead::init();
    Homestead::loadCredentials();
    Homestead::initRedis();

    $layoutConfig = Homestead::loadLayoutConfig();
    $allSectionsConfig = Homestead::loadAllSectionsConfig();

    $layoutStyles = $layoutConfig['layout'] ?? [];
    $favicon = $layoutStyles['favicon'] ?? null;
    $ogConfig = $layoutConfig['opengraph'] ?? [];

    // Загружаем все конфиги секций
    $sections = Homestead::loadSections($allSectionsConfig);

    // $plugins = Plugins::load_plugins( Homestead::loadYaml( CONFIG_PATH . DIRECTORY_SEPARATOR . '/_widgets.yml') );

} catch (RuntimeException|ParseException|RedisClientException|RedisException $e) {
    die($e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($layoutStyles['title'] ?? 'Resource Links') ?></title>

    <?php if ($favicon): ?>
    <link rel="icon" href="<?= htmlspecialchars($favicon) ?>" type="image/x-icon">
    <?php endif; ?>

    <!-- OpenGraph мета-теги -->
    <meta property="og:title" content="<?= htmlspecialchars($ogConfig['title'] ?? $layoutStyles['title'] ?? 'Resource Links') ?>">
    <meta property="og:type" content="<?= htmlspecialchars($ogConfig['type'] ?? 'website') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($ogConfig['url'] ?? (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
    <?php if (isset($ogConfig['image'])): ?>
    <meta property="og:image" content="<?= htmlspecialchars($ogConfig['image']) ?>">
    <meta property="og:image:width" content="<?= htmlspecialchars($ogConfig['image_width'] ?? '1200') ?>">
    <meta property="og:image:height" content="<?= htmlspecialchars($ogConfig['image_height'] ?? '630') ?>">
    <?php endif; ?>
    <meta property="og:description" content="<?= htmlspecialchars($ogConfig['description'] ?? $layoutStyles['description'] ?? 'Collection of useful resources') ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($ogConfig['site_name'] ?? $layoutStyles['title'] ?? 'Resource Links') ?>">
    <?php if (isset($ogConfig['locale'])): ?>
    <meta property="og:locale" content="<?= htmlspecialchars($ogConfig['locale']) ?>">
    <?php endif; ?>

    <style>
	body {
            font-family: <?= $layoutStyles['font-family'] ?? 'Arial, sans-serif' ?>;
            max-width: <?= $layoutStyles['max-width'] ?? '1200px' ?>;
            margin: 0 auto;
            padding: 20px;
            background-color: <?= $layoutStyles['background-color'] ?? '#fff' ?>;
            color: <?= $layoutStyles['text-color'] ?? '#333' ?>;
        }
        .section {
            margin-bottom: 30px;
        }
	.section-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 5px;
/*            border-bottom: 2px solid <?= $layoutStyles['section-border-color'] ?? '#eee' ?>; */
        }
	.section-icon {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        .section-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: <?= $layoutStyles['section-title-color'] ?? '#333' ?>;
            border-bottom: 2px solid <?= $layoutStyles['section-border-color'] ?? '#eee' ?>; 
            padding-bottom: 5px;
        }
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .resource-card {
            border: 1px solid <?= $layoutStyles['card-border-color'] ?? '#ddd' ?>;
            border-radius: 8px;
            padding: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            background-color: <?= $layoutStyles['card-bg-color'] ?? '#fff' ?>;
        }
        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: <?= $layoutStyles['card-hover-border-color'] ?? '#aaa' ?>;
        }
        .resource-icon {
            width: 32px;
            height: 32px;
            margin-right: 10px;
            vertical-align: middle;
        }
        .resource-title {
            font-size: 18px;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            color: <?= $layoutStyles['card-title-color'] ?? '#0066cc' ?>;
        }
        .resource-description {
            color: <?= $layoutStyles['card-description-color'] ?? '#666' ?>;
            margin: 10px 0 0 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($layoutStyles['header'] ?? 'Resource Links') ?></h1>

    <?php foreach ($sections as $section): ?>
        <div class="section">
            <div class="section-header">
                <?php if (isset($section['icon'])): ?>
                    <img src="<?= htmlspecialchars($section['icon']) ?>" alt="Section icon" class="section-icon">
                <?php endif; ?>
                <h2 class="section-title"><?= htmlspecialchars($section['title']) ?></h2>
            </div>

            <div class="resources-grid">
                <?php foreach ($section['resources'] as $resource): ?>
                    <a href="<?= htmlspecialchars($resource['link']) ?>" class="resource-card" target="_blank">
                        <h3 class="resource-title">
                            <?php if (isset($resource['icon'])): ?>
                                <img src="<?= htmlspecialchars($resource['icon']) ?>" alt="Icon" class="resource-icon">
                            <?php endif; ?>
                            <?= htmlspecialchars($resource['name']) ?>
                        </h3>
                        <?php if (isset($resource['description'])): ?>
                            <p class="resource-description"><?= htmlspecialchars($resource['description']) ?></p>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (!empty($plugins )): ?>
    <?php foreach ($plugins as $plugin): ?>
        <?= $plugin['html'] ?>
        <style><?= $plugin['css'] ?></style>
        <script><?= $plugin['js'] ?></script>
    <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

<?php
die;

