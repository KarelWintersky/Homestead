<?php

namespace Homestead;

class Homestead
{
    /**
     * @var string
     */
    private static string $_config_root;

    private static array $config_files = [];

    /**
     * @var mixed
     */
    public static mixed $_layout = [];

    /**
     * @var mixed
     */
    public static mixed $_sections_raw = [];

    /**
     * @var array|mixed
     */
    public static mixed $_sections;

    /**
     * @var mixed
     */
    public static mixed $_config;

    public static function init($root = __DIR__): void
    {
        if (!is_dir($root)) {
            throw new \RuntimeException("Missing config directory: {$root}");
        }

        self::$_config_root = $root;

        self::$config_files['config'] = self::$_config_root . '/_config.yml';
        self::$config_files['layout'] = self::$_config_root . '/_layout.yml';
        self::$config_files['sections'] = self::$_config_root . '/_sections.yml';
    }

    /**
     * Загружает файл с конфигами редис/sqlite
     *
     * @return mixed
     */
    public static function loadCredentials():mixed
    {
        $source = self::$config_files['config'];
        if (!file_exists($source)) {
            throw new \RuntimeException("Missing config/_config.yml file");
        }

        self::$_config = $systemConfig = Helper::loadYaml($source);

        return $systemConfig;
    }

    public static function loadLayoutConfig(): mixed
    {
        $source = self::$config_files['layout'];
        if (!file_exists($source)) {
            throw new \RuntimeException("Missing config/_layout.yml file");
        }

        $layoutConfig = Helper::loadYaml($source);

        // Гарантируем структуру assets с массивами по умолчанию
        $layoutConfig['assets'] = array_merge([
            'css' => [],
            'js' => [],
            'js_defer' => []
        ], $layoutConfig['assets'] ?? []);

        // Нормализуем все значения к массивам
        foreach (['css', 'js', 'js_defer'] as $key) {
            $layoutConfig['assets'][$key] = (array)$layoutConfig['assets'][$key];
        }

        self::$_layout = $layoutConfig;

        return $layoutConfig;
    }

    public static function loadSectionsConfig(): mixed
    {
        $source = self::$config_files['sections'];

        if (!file_exists($source)) {
            throw new \RuntimeException("Missing config/_sections.yml file");
        }
        $allSectionsConfig = Helper::loadYaml($source);

        if (!isset($allSectionsConfig['sections'])) {
            throw new \RuntimeException("Missing 'sections' in config/_sections.yml file");
        }

        self::$_sections_raw = $allSectionsConfig;

        return $allSectionsConfig;
    }

    public static function loadSections($allSectionsConfig):array
    {
        $sections = [];

        foreach ($allSectionsConfig['sections'] as $sectionFile) {
            if (!is_file(self::$_config_root . DIRECTORY_SEPARATOR . $sectionFile)) {
                continue;
            }

            $sectionConfig = Helper::loadYaml(self::$_config_root . DIRECTORY_SEPARATOR . $sectionFile);

            if ($sectionConfig && isset($sectionConfig['resources'])) {
                if (isset($sectionConfig['allow'])) {
                    if (!Helper::isIPAllowed($sectionConfig['allow'])) {
                        continue;
                    }
                }

                $filteredResources = [];
                foreach ($sectionConfig['resources'] as $resource) {

                    if (isset($sectionConfig['allow'])) {
                        $filteredResources[] = $resource;
                    }
                    elseif (isset($resource['allow'])) {
                        if (Helper::isIPAllowed($resource['allow'])) {
                            $filteredResources[] = $resource;
                        }
                    }
                    else {
                        $filteredResources[] = $resource;
                    }
                }

                if (!empty($filteredResources)) {
                    $sections[] = [
                        'title' => $sectionConfig['title'] ?? basename(self::$_config_root . DIRECTORY_SEPARATOR . $sectionFile, '.yml'),
                        'icon' => $sectionConfig['icon'] ?? null,
                        'resources' => $filteredResources
                    ];
                }

            }
        }

        self::$_sections = $sections;

        return $sections;
    }


}