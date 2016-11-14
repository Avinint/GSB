<?php 

namespace Core;

class Autoloader{
	
	static function register()
	{
		spl_autoload_register(array(__CLASS__, 'autoload'));
	}
	
	static function autoload($class)
	{
        $namespaces = array('App', 'Core');
        foreach($namespaces as $namespace){
            if(strpos($class, $namespace.'\\') === 0){
                $file = self::resolve($namespace, $class);

                if(file_exists($file)){
                    require $file;
                    break;
                }
            }
        }
    }

    static function resolve($namespace, $class)
    {
        $class = str_replace( '\\', D_S, $class);
        $file = dirname(__DIR__).D_S.$class.'.php';

        return $file;
    }
}