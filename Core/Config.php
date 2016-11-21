<?php

namespace Core;

class Config
{
	private $settings = [];
	private static $_instance;
	
	public static function getInstance($parameters, $config, $security)
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Config($parameters, $config, $security);
		}

		return self::$_instance;
	}
	
	public function __construct($parameters, $config, $security)
	{
		$this->settings = array_merge(
			 require($parameters), 
			 require($config), 
			 require($security));
	}

    public function load($parameters, $config, $security)
    {
        return self::$_instance;
    }
	
	public function get($key)
	{
		if(isset($key)){
			return $this->settings[$key];
		}else{
			return null;
		}
	}
}


