<?php

namespace Homestead;

use http\Exception\RuntimeException;
use Symfony\Component\Yaml\Yaml;

class Homestead
{
    public static array $configs = [];
    public static function init()
    {
        self::$configs['layout'] = CONFIG_PATH . '/_layout.yml';
        self::$configs['sections'] = CONFIG_PATH . '/_sections.yml';

    }

    public static function loadYamlConfig($filename): mixed
    {
        return Yaml::parseFile($filename);
    }

    /**
     * @return mixed|null
     */
    public static function loadLayoutConfig(): mixed
    {
        if (!file_exists(CONFIG_PATH . '/_layout.yml')) {
            throw new \RuntimeException("Missing config/_layout.yml file");
        }

        $layoutConfig = self::loadYamlConfig(CONFIG_PATH . '/_layout.yml');

        return $layoutConfig;
    }

    public static function loadAllSectionsConfig(): mixed
    {
        if (!file_exists(CONFIG_PATH . '/_sections.yml')) {
            throw new \RuntimeException("Missing config/_sections.yml file");
        }
        $allSectionsConfig = self::loadYamlConfig(CONFIG_PATH . '/_sections.yml');

        if (!isset($allSectionsConfig['sections'])) {
            throw new RuntimeException("Missing 'sections' in config/_sections.yml file");
        }

        return $allSectionsConfig;
    }

    public static function loadSections($allSectionsConfig):array
    {
        $sections = [];

        foreach ($allSectionsConfig['sections'] as $sectionFile) {
            $sectionConfig = self::loadYamlConfig(CONFIG_PATH . DIRECTORY_SEPARATOR . $sectionFile);

            if ($sectionConfig && isset($sectionConfig['resources'])) {
                $sections[] = [
                    'title' 	=> $sectionConfig['title'] ?? basename(CONFIG_PATH . DIRECTORY_SEPARATOR . $sectionFile, '.yml'),
                    'icon' 	=> $sectionConfig['icon'] ?? null,
                    'resources' => $sectionConfig['resources']
                ];
            }
        }

        return $sections;
    }

}