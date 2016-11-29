<?php

namespace Core\Component\Tool;

class Tool
{
    public function debug ($var)
    {
        highlight_string("<?php\n\$data =\n" . var_export($var, true) . ";\n?>");
    }
}