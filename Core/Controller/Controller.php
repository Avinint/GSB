<?php

namespace Core\Controller;

use \App;
use Core\View\View;

class Controller
{
    protected $viewpath;
    protected $container;
	
    public function __construct()
    {
        $this->initContainer();
        $this->viewpath = ROOT.'/App/View/';
    }

    protected function createForm($form)
    {
        $form  = '\App\Form\\'.ucfirst($form).'Form';
        return new $form();
    }

    protected function render($view, $variables = [], $template = 'default')
    {
        $view = new View($view);
        $view->render($variables, $template);
    }
	
	protected function generateURL($routeName, $parameters = array())
	{
		return $this->container['router']->generateURL($routeName, $parameters);
	}

    // TODO remove
    protected function loadModel($model)
    {
        $this->$model = $this->getTable($model);
    }

    protected function getTable($model)
    {
        $app = App::getInstance();

        return strpos($model, ':')?$app->getTable($model) : $app->getTableFromEntity($model);
    }

    public function controlAccess()
    {
        $ac = $this->container['access_control'];
        foreach ($ac as $rule) {
            if (preg_match('%'.$rule['path'].'%', $this->container['current_route']->getPath())) {
                foreach ($rule['roles'] as $role) {
                    if ($role === 'FREE_ACCESS' or true === $this->container['auth']->isGranted($role)) {
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
        if (false === $this->container['auth']->isGranted($role, $msg)) {
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
                foreach ($form->all() as $name => $def){

                    if ($def['type'] === 'password') {
                        if (isset($def['options']['confirmation']) || $fields[$name] === '') {
                            unset($fields[$name]);
                        }
                    }
                    if($def['type'] === 'hidden' && $name === 'action') {
                        unset($fields[$name]);
                    }
                }
                $this->cascadeRelations($fields, $files, $object);
                echo 'Donnees valides';

                $class = ucfirst($object['entity']->getClass());
                if ($object['entity']->getId()) {
                    $result = $this->getTable($class)->update(
                        $object['entity'], $fields, $files
                    );
                } else {
                    if(array_key_exists('date', $object['entity']->getVars())){
                        $fields['date'] = $this->insertDate();
                    }
                    $result = $this->getTable($class)->create(
                        $object['entity'],$fields, extract($files)
                    );
                }

                if ($result){
                    /* Si c'est une inscription on logue le nouvel utilisateur */
                    if (isset($object['login'])) {
                        $id =  $this->getTable($class)->lastInsertId();
                        $_SESSION['auth'] = $id;
                    }

                    $this->redirect($route);
                }
            } else {// fin validate
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

    private function initContainer()
    {
        $app = App::getInstance();
        $this->container = $app->getContainer();
    }

}