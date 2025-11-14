<?php

namespace Homestead;

class Template
{
    public static function render_template($template, $data = []): bool|string
    {
        extract($data);
        ob_start();
        include $template;
        return ob_get_clean();
    }



}