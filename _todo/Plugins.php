<?php

namespace _todo;

class Plugins
{
    private static mixed $root;

    public static function init($root): void
    {
        self::$root = $root;
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

            $pluginPath = self::$root . '/plugins/' . $pluginName . '/plugin.php';

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