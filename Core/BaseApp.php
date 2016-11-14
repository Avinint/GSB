<?php 

use Core\Config;
use Core\Database\MySQLDatabase;
use Core\Component\Router;
use Core\Component\Exception\ExceptionHandler;

abstract class BaseApp
{
	protected static $instance;
	protected $db;
    protected $routing;
	protected $accessControl;
    protected $exceptionHandler;
    protected $environment;
	
	public static function getInstance($environment = 'prod')
	{
		if(is_null(self::$instance))
		{
			self::$instance = new App($environment);
            self::$instance->exceptionHandler =  ExceptionHandler::register($environment);
            self::$instance->routing = new Router();
		}

		return self::$instance;
	}

    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    public function getRouter()
    {
        return $this->routing;
    }

    public function getEnvironment()
    {
        return $this->environment;
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
	  return Config::getInstance(ROOT.'/App/Config/dbConfig.php', ROOT.'/App/Config/config.php', ROOT.'/App/Config/security.php');
	}
	
	public function getDb()
	{
		if(is_null($this->db)){
			$config = $this->getConfig();
			$this->db = new MySQLDatabase($config->get('db_name'), $config->get('db_user'), $config->get('db_pass'), $config->get('db_host'));
		}

		return $this->db;
  }

	public function getAccessControl()
	{
		if(is_null($this->accessControl)){
			$this->accessControl = $this->getConfig()->get('access_control');
		}
		
		return $this->accessControl;
    }

    public function getExceptionHandler()
    {
        return $this->exceptionHandler;
    }
}