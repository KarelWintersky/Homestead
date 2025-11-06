<?php

namespace Homestead;

use Arris\Toolkit\RedisClient;
use Arris\Toolkit\RedisClientException;
use RedisException;
use Symfony\Component\Yaml\Yaml;

class Homestead
{
    public static array $config_files = [];

    public static mixed $_config;
    public static mixed $_layout;
    public static mixed $_sections_raw;
    public static mixed $_sections;

    public static RedisClient|null $redis = null;

    public static function init(): void
    {
        self::$config_files['config'] = CONFIG_PATH . '/_config.yml';
        self::$config_files['layout'] = CONFIG_PATH . '/_layout.yml';
        self::$config_files['sections'] = CONFIG_PATH . '/_sections.yml';
    }

    /**
     * @throws RedisClientException
     * @throws RedisException
     */
    public static function initRedis(): void
    {
        $redis = new \Arris\Toolkit\RedisClient();

        $credentials = self::$_config;

        if (isset($credentials['redis'])) {
            $cr = $credentials['redis'];
            $redis->connect($cr['host'], $cr['port'], $cr['database'], $cr['enable']);
        }

        Homestead::$redis = $redis;
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
            throw new \RuntimeException("Missing 'sections' in config/_sections.yml file");
        }

        self::$_sections_raw = $allSectionsConfig;

        return $allSectionsConfig;
    }

    public static function loadSections($allSectionsConfig):array
    {
        $sections = [];

        foreach ($allSectionsConfig['sections'] as $sectionFile) {
            if (!is_file(CONFIG_PATH . DIRECTORY_SEPARATOR . $sectionFile)) {
                continue;
            }

            $sectionConfig = self::loadYaml(CONFIG_PATH . DIRECTORY_SEPARATOR . $sectionFile);

            if ($sectionConfig && isset($sectionConfig['resources'])) {
                if (isset($sectionConfig['allow'])) {
                    if (!Helper::isIPAllowed($sectionConfig['allow'])) {
                        continue; // Пропускаем секцию, если IP не разрешен
                    }
                }

                // Фильтруем ресурсы по IP-доступу
                $filteredResources = [];
                foreach ($sectionConfig['resources'] as $resource) {
                    // Если для секции уже есть разрешение, ресурс показывается без проверки
                    if (isset($sectionConfig['allow'])) {
                        $filteredResources[] = $resource;
                    }

                    // Если для ресурса указана своя директива allow, проверяем её
                    elseif (isset($resource['allow'])) {
                        if (Helper::isIPAllowed($resource['allow'])) {
                            $filteredResources[] = $resource;
                        }
                    }

                    // Если у ресурса нет ограничений, показываем его
                    else {
                        $filteredResources[] = $resource;
                    }
                }

                // Добавляем секцию только если в ней есть ресурсы после фильтрации
                if (!empty($filteredResources)) {
                    $sections[] = [
                        'title'     => $sectionConfig['title'] ?? basename(CONFIG_PATH . DIRECTORY_SEPARATOR . $sectionFile, '.yml'),
                        'icon'      => $sectionConfig['icon'] ?? null,
                        'resources' => $filteredResources
                    ];
                }

            } // if
        } // foreach sections

        self::$_sections = $sections;

        return $sections;
    }



}