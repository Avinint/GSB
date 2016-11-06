<?php

namespace Core;
/*
class Config
{
	private $settings = [];
	private static $_instance;
	
	public static function getInstance($file)
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Config($file);
		}
		return self::$_instance;
	}
	
	public function __construct($file)
	{
		$this->settings = require($file);
	}
	
	public function get($key)
	{
		if(isset($key)){
			return $this->settings[$key];
		}else{
			return null;
		}
	}
}*/

class Config
{
	private $settings = [];
	private static $_instance;
	
	public static function getInstance($parameters, $config)
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Config($parameters, $config);
		}
		return self::$_instance;
	}
	
	public function __construct($parameters, $config)
	{
		var_dump(require($parameters));
		$this->settings = array_merge(require($parameters), require($config));
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


