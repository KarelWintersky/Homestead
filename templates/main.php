<?php
global $favicon, $sections, $layoutConfig;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($layoutStyles['title'] ?? 'Resource Links') ?></title>
    <?php if ($favicon): ?><link rel="icon" href="<?= htmlspecialchars($favicon) ?>" type="image/x-icon"> <?php endif; ?>

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

    <script src="/assets/scripts.js"></script>
    <link href="/assets/styles.css" rel="stylesheet">

    <style data-comment="patch">
        body {
            font-family: <?= $layoutStyles['font-family'] ?? 'Arial, sans-serif' ?>;
            max-width: <?= $layoutStyles['max-width'] ?? '1200px' ?>;
            background-color: <?= $layoutStyles['background-color'] ?? '#fff' ?>;
            color: <?= $layoutStyles['text-color'] ?? '#333' ?>;
        }
        .section-title {
            color: <?= $layoutStyles['section-title-color'] ?? '#333' ?>;
            border-bottom: 2px solid <?= $layoutStyles['section-border-color'] ?? '#eee' ?>;
        }
        .resource-card {
            border: 1px solid <?= $layoutStyles['card-border-color'] ?? '#ddd' ?>;
            background-color: <?= $layoutStyles['card-bg-color'] ?? '#fff' ?>;
        }
        .resource-card:hover {
            border-color: <?= $layoutStyles['card-hover-border-color'] ?? '#aaa' ?>;
        }
        .resource-title {
            color: <?= $layoutStyles['card-title-color'] ?? '#0066cc' ?>;
        }
        .resource-description {
            color: <?= $layoutStyles['card-description-color'] ?? '#666' ?>;
        }
    </style>

    <?php foreach($layoutConfig['assets']['css'] as $f): ?>
    <link href="<?= printf($f); ?>" rel="stylesheet">
    <?php endforeach; ?>

    <?php foreach($layoutConfig['assets']['js'] as $f): ?>
    <script src="<?= printf($f); ?>"></script>
    <?php endforeach; ?>

    <?php foreach($layoutConfig['assets']['js_defer'] as $f): ?>
    <script src="<?= printf($f); ?>" defer></script>
    <?php endforeach; ?>
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

</body>
</html>
