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
            self::$instance->exceptionHandler =  ExceptionHandler::register($environment);
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
        Core\Container\ContainerBuilder::init($this->container);
		$this->router = $this->getContainer('router');
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public static function load()
    {
        session_start();
        require_once ROOT.'/Core/Autoloader.php';
        require_once ROOT.'/vendor/autoload.php';
        \Core\AutoLoader::register();
    }

    public function getTable($name = '')
    {
        $name = explode(':', $name);
        $module = array_shift($name);
        $name = array_pop($name);

        $className = 'App\\'.$module.'\\Table\\'.ucfirst($name).'Table';
        if (!class_exists($className)) {
            $className = 'Core\\Table\\Table';
        }

        return new $className($name);
    }

    public function getConfig()
    {
       return $this->getContainer('config');
        //return Config::getInstance(ROOT.'/App/Config/dbConfig.php', ROOT.'/App/Config/config.php', ROOT.'/App/Config/security.php');
    }

    public function getDb()
    {
        if (is_null($this->db)) {
            $config = $this->getConfig();
            $this->db = new MySQLDatabase($config->get('db_name'), $config->get('db_user'), $config->get('db_pass'), $config->get('db_host'));
        }

        return $this->db;
    }

    public function getAccessControl()
    {
        if (is_null($this->accessControl)) {
            $this->accessControl = $this->getConfig()->get('access_control');
        }

        return $this->accessControl;
    }

    public function getExceptionHandler()
    {
        return $this->exceptionHandler;
    }

    public function decamelize($string)
    {
        return strtolower(preg_replace('/(?|([a-z\d])([A-Z])|([^\^])([A-Z][a-z]))/', '$1_$2', $string));
    }

}