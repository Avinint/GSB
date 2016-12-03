<?php

namespace Core\Component\Database;

class DataMapperPath
{
    public function load($module, $class)
    {
        return new DataMapper($class);
    }
} 