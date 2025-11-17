<?php

namespace Homestead;

class Template
{
    public static function render_template($file, $data = []): bool|string
    {
        extract($data);
        ob_start();
        include $file;
        return ob_get_clean();
    }



}