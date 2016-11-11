<?php

namespace Core\Service;

use Core\Service\DbAuth;
use Core\Service\Route;
use Core\Controller\Controller;

class Routing
{
    private $routes = array();
		private $currentRoute;

    public function __construct()
    {
        $this->initRoutes(require ROOT.D_S.'App'.D_S.'Config'.D_S.'routes.php');
				$this->currentRoute = new Route();
    }

    public function createRoute(array  $route)
    {
        //return new Route($route['name'], $route['path']);
    }

    public function addRoute(array $route)
    {
        $this->routes[] = $route;
    }

   public function getRoute($name = '', $path = '')
   {
       echo $_SERVER["REQUEST_URI"];
   }

    public function initRoutes(array $routes = array())
    {
       $this->routes = $routes;
    }

    public function getParams(&$path)
    {
        $start = strpos($path, '/{');
        $end = strpos($path, '}');
        $subString = substr($path, $start, $end-$start+1);
        $path = str_replace($subString, '', $path);
        $key = rtrim($subString,'}');
        $key = ltrim($key, '/{') ;

        return $key;
    }

    public function routeMatch($routePath)
    {
        $params = array();
        $tempRoutePath = '';
        $path = '';

        foreach($this->routes as $name => $route)
        {
            $count = 0;
            $path = $route['path'];

            // On récupère les caractères entre accolades comme paramètres de la route
            while(strpos($path, '{') !== false && strpos($path, '}') !== false){
                $key = $this->getParams($path);
                $count++;
                $params[$key] = null;
                // le nom est stocké comme clé dans un tableau associatif  vide
                // On remplira le tableau avec les valeurs récupérés dans l
            }

            //echo 'route collection path: "'.$path.'"'.BR;
            //echo 'route match path: "'.$routePath.'"'.BR;
            $tempRoutePath = $routePath;
            $data = '';
            if($tempRoutePath !== $path){
                $data = str_replace($path.'/', '', $tempRoutePath);
                $data = ltrim($data, '/');
                //echo BR.'data: '.$data;
                //$tempRoutePath = str_replace('/'.$data, '', $tempRoutePath);
                //echo BR.'route match path: '.$tempRoutePath;
                $data = explode('/', $data);
                $paramKeys = array_keys($params);

                foreach ($paramKeys as $value => $key){
                    if($value <  count($data)){
                            $params[$key] = $data[$value];
                    }
                }
            }

            // stripos: pour differencier les routes avec des parametres de l'equivalent sans
            if(($path === $routePath)xor(stripos($tempRoutePath, $path) !== false && $count > 0)) {
                $this->dispatch($route['controller'], $params);
                return true;
            }
        }
       // header('Location:'.ROOT.'/App/View/Messages/404.html', 404);
        //throw new \RuntimeException("HTTP/1.0 404 Not Found", 404);

        return false;
    }


    public function dispatch($controller,  $params = array())
    {
        $controller = explode(':', $controller);
        $action = array_pop($controller);

        $controller = implode('\\', $controller);
        $controller = 'App\Controller\\'. $controller;

        $controller = new $controller();
        if(!$controller instanceof Controller){

            throw new \Exception("Something wrong happened...");
        }
        //$controller->controlAccess($this->currentRoute);
        if(isset($params)){
            $controller->$action($params);
        }else{
            $controller->$action();
        }
    }

    public function resolveRoute()
    {
        $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';

        $uri = strtolower(substr($_SERVER['REQUEST_URI'], strlen($basepath)));
        //echo 'uri: '.$uri.BR;
        $uri = str_replace('index.php', '', $uri);
        $uri =  '/'.trim($uri, '/');
        $this->currentRoute->setPath($uri);

        return $this->routeMatch($uri);
    }

    public function generateRoute($routeName, $parameters = array())
    {
        foreach($this->routes as $name => $route)
        {
            if($name == $routeName) {
                //$path = $route['path'];
                $this->dispatch($route['controller'], $parameters);
            }
        }

        return null;
    }

    public function generatePath($path)
    {
        $uri = '//'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
        //$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';;
        $uri = str_replace('/index.php', '', $uri);
        $basepath = rtrim($uri,'/');
        $path = $basepath.$path;

        return $path;
    }

    public function generateURL($routeName, $parameters = array())
    {
        $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $basepath = rtrim($basepath,'/');
        foreach($this->routes as $name => $route)
        {
            if($name == $routeName) {
                $path = $basepath.$route['path'];

                if($parameters){
                    $params = array();
                    while(strpos($path, '{') !== false && strpos($path, '}') !== false){

                        $key = $this->getParams($path);
                        $params[$key] = null;
                    }
                    foreach($parameters as $key =>$value)
                    {
                        $params[$key] = $value;
                    }

                    $str = implode('/', $params);

                    $path = $path.'/'.$str;
                }
                //$this->dispatch($route['controller'], $parameters);
            }
        }

        return null;
    }
}
