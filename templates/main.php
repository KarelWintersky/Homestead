<?php
$currentYear = date('Y');
$copyrightYear = ($currentYear == 2025) ? '2025' : "2025&mdash;$currentYear";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="copyright" content="ООО Психотроника, <?= $copyrightYear ?>">
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

    <script>
        window.engine_options = window.engine_options || {};
        document.addEventListener('DOMContentLoaded', function() {
        });
    </script>
    <style data-comment="main">
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            color: #333;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 5px;
        }
        .section-icon {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        .section-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .resource-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            background-color: #fff;
        }
        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #aaa;
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
            color: #0066cc;
        }
        .resource-description {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 14px;
        }
        .header-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        h1 {
            margin: 0;
        }
    </style>

    <style data-comment="patch">
        body {
            font-family: <?= $layoutStyles['font-family'] ?? 'Arial, sans-serif' ?>;
            max-width: <?= $layoutStyles['max-width'] ?? '1200px' ?>;
        <?php if (isset($layoutStyles['background']['image'])): ?>
            background-image: url('<?= htmlspecialchars($layoutStyles['background']['image']) ?>');
            background-size: <?= $layoutStyles['background']['size'] ?? 'cover' ?>;
            background-position: <?= $layoutStyles['background']['position'] ?? 'center' ?>;
            background-repeat: <?= $layoutStyles['background']['repeat'] ?? 'no-repeat' ?>;
            background-attachment: <?= $layoutStyles['background']['attachment'] ?? 'scroll' ?>;
        <?php if (isset($layoutStyles['background']['color'])): ?>
            background-color: <?= $layoutStyles['background']['color'] ?>;
        <?php endif; ?>
        <?php elseif (isset($layoutStyles['background']['color'])): ?>
            background-color: <?= $layoutStyles['background']['color'] ?>;
        <?php else: ?>
            background-color: <?= $layoutStyles['background-color'] ?? '#fff' ?>;
        <?php endif; ?>
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

        .header-icon {
            width: <?= $layoutStyles['header-icon-size'] ?? '40px' ?>;
            height: <?= $layoutStyles['header-icon-size'] ?? '40px' ?>;
            object-fit: contain;
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

<div class="header-container">
    <?php if (isset($layoutStyles['header-icon'])): ?>
        <img src="<?= htmlspecialchars($layoutStyles['header-icon']) ?>" alt="Header icon" class="header-icon">
    <?php endif; ?>
    <h1><?= htmlspecialchars($layoutStyles['header'] ?? 'Resource Links') ?></h1>
</div>

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
<!-- © ООО Психотроника <?= $copyrightYear ?> -->
