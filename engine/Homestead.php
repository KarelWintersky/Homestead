<?php

namespace Homestead;

use Arris\Toolkit\RedisClient;
use http\Exception\RuntimeException;
use Symfony\Component\Yaml\Yaml;

class Homestead
{
    public static array $config_files = [];

    public static $_config;
    public static $_layout;
    public static $_sections_raw;
    public static $_sections;

    public static RedisClient|null $redis = null;

    public static function init()
    {
        self::$config_files['config'] = CONFIG_PATH . '/_config.yml';
        self::$config_files['layout'] = CONFIG_PATH . '/_layout.yml';
        self::$config_files['sections'] = CONFIG_PATH . '/_sections.yml';
    }

    public static function loadCredentials():mixed
    {
        $source = self::$config_files['config'];
        if (!file_exists($source)) {
            throw new \RuntimeException("Missing config/_config.yml file");
        }

        self::$_config = $systemConfig = self::loadYaml($source);

        return $systemConfig;
    }

    public static function loadYaml($filename): mixed
    {
        return Yaml::parseFile($filename);
    }

    /**
     * @return mixed|null
     */
    public static function loadLayoutConfig(): mixed
    {
        $source = self::$config_files['layout'];
        if (!file_exists($source)) {
            throw new \RuntimeException("Missing config/_layout.yml file");
        }

        self::$_layout = $layoutConfig = self::loadYaml($source);

        return $layoutConfig;
    }

    public static function loadAllSectionsConfig(): mixed
    {
        $source = self::$config_files['sections'];

        if (!file_exists($source)) {
            throw new \RuntimeException("Missing config/_sections.yml file");
        }
        $allSectionsConfig = self::loadYaml($source);

        if (!isset($allSectionsConfig['sections'])) {
            throw new RuntimeException("Missing 'sections' in config/_sections.yml file");
        }

        self::$_sections_raw = $allSectionsConfig;

        return $allSectionsConfig;
    }

    public static function loadSections($allSectionsConfig):array
    {
        $sections = [];

        foreach ($allSectionsConfig['sections'] as $sectionFile) {
            $sectionConfig = self::loadYaml(CONFIG_PATH . DIRECTORY_SEPARATOR . $sectionFile);

            if ($sectionConfig && isset($sectionConfig['resources'])) {
                $sections[] = [
                    'title' 	=> $sectionConfig['title'] ?? basename(CONFIG_PATH . DIRECTORY_SEPARATOR . $sectionFile, '.yml'),
                    'icon' 	=> $sectionConfig['icon'] ?? null,
                    'resources' => $sectionConfig['resources']
                ];
            }
        }

        self::$_sections = $sections;

        return $sections;
    }

    /**
     *
     * @param $section
     * @param $redis
     * @return array
     */
    public static function load_plugins($section, $redis = null): array
    {
        $pluginsOutput = [];

        foreach ($section['plugins'] ?? [] as $pluginName => $pluginOptions) {

            if (!$pluginOptions['enable']) continue;

            $pluginPath = INSTALL_PATH . '/plugins/' . $pluginName . '/plugin.php';

            if (file_exists($pluginPath)) {
                $pluginFunction = include $pluginPath;
                if (is_callable($pluginFunction)) {
                    $pluginsOutput[$pluginName] = $pluginFunction($pluginOptions);

                    // Добавляем отметку о кешировании
                    if ($redis && isset($pluginsOutput[$pluginName]['_cached'])) {
                        $pluginsOutput[$pluginName]['_cached'] = 'Yes (until '.date(
                                'H:i:s',
                                $pluginsOutput[$pluginName]['_cached'] + 1800
                            ).')';
                    } else {
                        $pluginsOutput[$pluginName]['_cached'] = 'No';
                    }
                }
            }
        }

        return $pluginsOutput;
    }

}