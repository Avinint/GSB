<?php 

use Core\Config;
use Core\Database\MySQLDatabase;
use Core\Component\Router;
use Core\Component\Exception\ExceptionHandler;
use Pimple\Container;


abstract class BaseApp
{
    protected static $instance;
    protected $db;
    protected $router;
    protected $accessControl;
    protected $exceptionHandler;
    protected $environment;
    protected $container;

    public static function getInstance($environment = 'prod')
    {
        if(is_null(self::$instance))
        {
            self::$instance = new App($environment);
		}

        return self::$instance;
    }

    public function getContainer($service = '')
    {
        if (empty($service)) {
            return $this->container;
        }

        return $this->container[$service];
    }

    public function __construct($environment)
    {
        $this->environment = $environment;
		
        $this->container = new Container();
		$this->container['env'] = $environment;
		//$this->exceptionHandler = ExceptionHandler::register($environment);
        Core\Container\ContainerBuilder::init($this->container);
		$this->exceptionHandler = $this->getContainer('exception_handler');
    }

    public function getRouter()
    {
        return $this->getContainer('router');
    }

    public function getEnvironment()
    {
        return $this->getContainer('env');
    }

    public static function load()
    {
        session_start();
        require_once ROOT.'/Core/Autoloader.php';
        require_once ROOT.'/vendor/autoload.php';
        \Core\AutoLoader::register();
    }

    public function getTable($fullName = '')
    {
        $name = explode(':', $fullName);
        $module = array_shift($name);
        $name = array_pop($name);

        $className = 'App\\'.$module.'\\Table\\'.ucfirst($name).'Table';
        if (!class_exists($className)) {
            $className = 'Core\\Table\\Table';
        }

        return new $className($fullName);
    }

    public function getConfig()
    {
       return $this->getContainer('config');
        //return Config::getInstance(ROOT.'/App/Config/dbConfig.php', ROOT.'/App/Config/config.php', ROOT.'/App/Config/security.php');
    }

    public function getDb()
    {
        return $this->getContainer('db');
    }

    public function decamelize($string)
    {
        return strtolower(preg_replace('/(?|([a-z\d])([A-Z])|([^\^])([A-Z][a-z]))/', '$1_$2', $string));
    }
}