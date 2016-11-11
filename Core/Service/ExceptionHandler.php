<?php

namespace Core\Service;


class ExceptionHandler
{
    public static function register($handler = null)
    {
        var_dump("ziziziz");
        if(!$handler instanceof self) {
            $handler = new static();
        }
        var_dump("ziziziz");

        //set_error_handler(array($handler, 'handleException'));

        return $handler;
    }

    public function handleException(\Exception $e)
    {
        sprintf('Uncaught Exception %s: "%s" at %s line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
    }
} 