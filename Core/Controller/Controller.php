<?php

namespace Core\Controller;

use \App;
use Core\View\View;
use Core\Component\Router;
use Core\Component\Route;
use Core\Component\DbAuth;

class Controller
{
    protected $viewpath;
    protected $route;
    protected $auth;
	
    public function __construct()
    {
        // TODO Appliquer control access access  en fonction du fichier security.php
        $this->viewpath = ROOT.'/App/View/';
        $this->route = new Router();
        $this->auth =  new DbAuth();
    }

    protected function createForm($form)
    {
        $form  = '\App\Form\\'.ucfirst($form).'Form';
        return new $form();
    }

    protected function render($view, $variables = [])
    {
        $view = new View($view);
        $view->render($variables);
    }

    // TODO remove
    protected function loadModel($model)
    {
        $this->$model = $this->getTable($model);
    }

    protected function getTable($model)
    {
        $app = App::getInstance();

        return $app->getTable($model);
    }

    public function controlAccess(Route $route)
    {
        $app = App::getInstance();
        $ac = $app->getAccessControl();
        foreach ($ac as $rule) {
            if (preg_match('/'.$rule['path'].'/', $route->getPath())) {
                foreach ($rule['roles'] as $role) {
                    if (true === $this->auth->isGranted($role)) {
                        var_dump('good');
                        return;
                    }
                }
                $this->forbidden('Impossible d\'accéder à cette page!');
            }
        }
        // TODO finir donner acces à la current_route
    }

    protected function filterAccess($role = 'ROLE_DEFAULT', $msg = 'Impossible d\'accéder à cette page!')
    {
        $app = App::getInstance();
        $auth = new DbAuth();
        if (false === $this->auth->isGranted($role, $msg)) {
            $this->forbidden($msg);
            // TODO  créer createAccessDeniedException
        }
    }

    // Adds or changes the date when inserting in database or updating
    protected function insertDate($format = 'Y-m-d H:i:s')
    {
        return date($format);
    }

    protected function getLastInsertId()
    {
        return \App::getInstance()->getDb()->lastInsertId();
    }

    protected function forbidden($msg = 'Accès interdit')
    {
        header('HTTP/1.0 403 Forbidden');
        die($msg);
    }

    protected function notFound()
    {
        header('HTTP/1.0 404 Not Found');
        die('Page introuvable');
    }

    public function redirect($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        throw new \Exception("redirection"); // TDODO test and remove?
    }

    public function handleRequest($form, $object, $route)
    {
        if(!empty($_POST) || !empty($_FILES)){

            $fields = $form->parseFields($_POST);
            $files = $form->parseFields($_FILES);
            $result = null;

            if($form->validate($fields)){

                if(isset($object['fk'])){

                    foreach($object['fk'] as $k => $v){
                        if(array_key_exists($k, $fields)){
                            $fields[$v] = $fields[$k];
                            unset($fields[$k]);
                        }
                    }
                }
                foreach ($form->all() as $name => $options){
                    if ($options['type'] === 'password'){
                        if(isset($options['confirmation']) || $fields[$name] === ''){

                            unset($fields[$name]);
                        }
                    }
                }
                $this->cascadeRelations($fields, $files, $object);
                echo 'Donnees valides';

                $class = ucfirst($object['entity']->getClassName());
                if($object['entity']->getId()){
                    $result = $this->$class->update(
                        $object['entity'], $fields, $files
                    );
                }else{
                    if(array_key_exists('date', $object['entity']->getVars())){
                        $fields['date'] = $this->insertDate();
                    }
                    $result = $this->$class->create(
                        $object['entity'],$fields, extract($files)
                    );
                }

                if($result){
                    /* Si c'est une inscription on logue le nouvel utilisateur */
                    if(isset($object['login'])){
                        $id =  $this->$class->lastInsertId();
                        $_SESSION['auth'] = $id;
                    }
                    // TO DO redirect
                    //var_dump($this->route->generateRoute('admin_article_index'));
                    $this->redirect($route);
                }
            }else{// fin validate
                echo 'formulaire non valide';
            }
        }
    }

    public function cascadeRelations(&$fields, &$files, $object)
    {
        $result = null;
        $childObject = array();
        $childFiles = array();

        if(isset($object['children'])){
            $children = $object['children'];
            foreach($children as $class => $child){
                $object = $child['entity'];
                // var_dump($object->getVars());
                if($object){
                    foreach ($object->getVars() as $key => $value){

                        if(array_key_exists($key, $fields)){
                            $childObject = array_intersect_key($fields, $object->getVars());
                            $fields = array_diff_key($fields, $object->getVars());
                        }
                        if(array_key_exists($key, $files)){
                            $childFiles = array_intersect_key($files, $object->getVars());
                            $files = array_diff_key($files, $object->getVars());
                        }
                    }

                    if($object->getId() === null){
                        $result = $this->$class->create(
                            $object, $childObject, $childFiles, $class
                        );
                        if($result){
                            $id =  $this->$class->lastInsertId();
                            $fields[$children[key($children)]['db_name']] = $id;
                        }
                    }else{
                        $result = $this->$class->update(
                            $object, $childObject, $childFiles, $class
                        );
                    }
                }
            }
        }else{
            $fields = array_intersect_key($fields, $object['entity']->getVars());
            $files = array_intersect_key($files, $object['entity']->getVars());
        }
    }

    public function get($service)
    {
        $app = App::getInstance();
        $container = $app->getContainer();

        return $container[$service];
    }
}