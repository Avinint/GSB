<?php

namespace Core\Service;


class ExceptionHandler
{
    private $environment;

    public static function register($environment, $handler = null)
    {
        if(!$handler instanceof self) {
            $handler = new static();
        }
        $handler->environment = $environment;

        set_exception_handler(array($handler, 'handleException'));

        return $handler;
    }

    public function handleException(\Exception $e)
    {
        if($this->environment === "dev"){
            echo sprintf('Uncaught %s: "%s" at %s line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
        }

        return false;
    }


} 