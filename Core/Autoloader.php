<?php 

namespace Core;

class Autoloader{
	
	static function register()
	{
		spl_autoload_register(array(__CLASS__, 'autoload'));
	}
	
	static function autoload($class)
	{
		if(strpos($class, __NAMESPACE__.'\\') === 0){
			$class = str_replace(__NAMESPACE__.'\\', '', $class);
           $class = str_replace( '\\', D_S, $class);
			require __DIR__.D_S.$class.'.php';
		}	
	}
}