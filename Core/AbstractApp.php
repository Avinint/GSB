<?php 

use \Core\Config;
use \Core\Database\MySQLDatabase;
use \Core\Service\Routing;

abstract class AbstractApp
{
	protected static $_instance;
	protected $db_instance;
    protected $routing;
	protected $ac_instance;
	
	public static function getInstance()
	{

		if(is_null(self::$_instance))
		{
			self::$_instance = new App();
            self::$_instance->routing = new Routing();
		}
		return self::$_instance;
	}

    public function getRouting()
    {
        return $this->routing;
    }

	public static function load()
	{
		session_start();
		require ROOT.'/App/Autoloader.php';
		\App\AutoLoader::register();
		require ROOT.'/Core/Autoloader.php';
		\Core\AutoLoader::register();
	}
	
	public function getTable($name)
	{
		$className = '\\App\\Table\\'.ucfirst($name).'Table';
		if(!class_exists($className)){
				$className = '\\Core\\Table\\Table';
		}
		
		return new $className($this->getDb());
	}
	
	public function getConfig()
	{
	  return Config::getInstance(ROOT.'/App/Config/DbConfig.php', ROOT.'/App/Config/Config.php', ROOT.'/App/Config/Config.php');
	}
	
	public function getDb()
	{
		if(is_null($this->db_instance)){
			$config = $this->getConfig();
			$this->db_instance = new MySQLDatabase($config->get('db_name'), $config->get('db_user'), $config->get('db_pass'), $config->get('db_host'));
		}
		
		return $this->db_instance;
  }

	public function getAccessControl()
	{
		if(is_null($this->ac_instance)){
			$this->ac_instance = $this->getConfig()->get('access_control');
		}
		
		return $this->ac_instance;
  }
}