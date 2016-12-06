<?php

namespace Core\Component\Tool;

class Tool
{
    public function debug ($var)
    {
        highlight_string("<?php\n\$data =\n" . var_export($var, true) . ";\n?>");
    }

    public function decamelize($string)
    {
        $string = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string));

        return $string;
    }
}