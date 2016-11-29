<?php

namespace Core\Component\Database;

class DataMapperFactory
{
    public function load($className)
    {
        return new DataMapper($className);
    }
} 