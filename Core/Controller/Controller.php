<?php

namespace Core\Controller;

use \App;
use Core\View\View;
use Core\Container\ContainerAware;

class Controller extends ContainerAware
{
    protected $viewpath;

    public function __construct()
    {
        parent::__construct();
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
        return strpos($model, ':')? $app->getTable($model) : $app->getTableFromEntity($model);
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
        header('Location: ' . $url);
        throw new \Exception("redirection"); // TDODO test and remove?
    }

    public function handleRequestBack($form, $data, $route)
    {
        if(!empty($_POST) || !empty($_FILES)){

            $fields = $form->parseFields($_POST);
            $files = $form->parseFields($_FILES);
            $result = null;

            if($form->validate($fields)){

                if(isset($data['fk'])){
                    foreach($data['fk'] as $k => $v){
                        if(array_key_exists($k, $fields)){
                            $fields[$v] = $fields[$k]; // $fields['role_id'] = $fields ['role']
                            unset($fields[$k]);  //unset role
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
                $this->cascadeRelations($fields, $files, $data);
                echo 'Donnees valides';

                $class = ucfirst($data['entity']->getClass());
                if ($data['entity']->getId()) {
                    $result = $this->getTable($class)->update(
                        $data['entity'], $fields, $files
                    );
                } else {
                    if(array_key_exists('date', $data['entity']->getVars())){
                        $fields['date'] = $this->insertDate();
                    }
                    $result = $this->getTable($class)->create(
                        $data['entity'],$fields, extract($files)
                    );
                }

                if ($result) {
                    /* Si c'est une inscription on logue le nouvel utilisateur */
                    if (isset($data['login'])) {
                        $id = $this->getTable($class)->lastInsertId();
                        $_SESSION['auth'] = $id;
                    }

                    $this->redirect($route);
                }
            } else {// fin validate
                echo 'formulaire non valide';
            }
        }
    }

    public function handleRequest($form, $data)
    {
        if(!empty($_POST) || !empty($_FILES)){

            $fields = $form->parseFields($_POST);
            $files = $form->parseFields($_FILES);
            $result = null;

            if ($form->validate($fields)) {
                if(isset($data['fk'])){
                    foreach($data['fk'] as $k => $v){
                        if(array_key_exists($k, $fields)){
                            $fields[$v] = $fields[$k]; // $fields['role_id'] = $fields ['role']
                            unset($fields[$k]);  //unset role
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

                $this->cascadeRelations($fields, $files, $data);

                $entity = ($data['entity']);
                foreach ($fields as $attr => $value) {
                    $method = 'set'.ucfirst($attr);
                    $entity->$method($value);
                }

            } else {// fin validate
                echo 'formulaire non valide';
            }
        }
    }

    public function cascadePersist(&$fields, &$files, $data)
    {
        $result = null;
        $childObject = array();
        $childFiles = array();

        /*if (isset($data['children'])) {
            $children = $data['children'];
            foreach ($children as $class => $child) {
                $data = $child['entity'];
                // var_dump($object->getVars());
                if ($data) {
                    foreach ($data->getVars() as $key => $value) {
                        if (array_key_exists($key, $fields)) {
                            $childObject = array_intersect_key($fields, $data->getVars());
                            $fields = array_diff_key($fields, $data->getVars());
                        }
                        if (array_key_exists($key, $files)) {
                            $childFiles = array_intersect_key($files, $data->getVars());
                            $files = array_diff_key($files, $data->getVars());
                        }
                    }

                    if ($data->getId() === null) {
                        $result = $this->getTable($class)->create(
                            $data, $childObject, $childFiles, $class
                        );
                        if ($result) {
                            $id =  $this->getTable($class)->lastInsertId();

                            $fields[$children[key($children)]['db_name']] = $id;
                        }
                    } else {
                        $result = $this->getTable($class)->update(
                            $data, $childObject, $childFiles, $class
                        );
                    }
                }
            }
        } else {
            $fields = array_intersect_key($fields, $data['entity']->getVars());
            $files = array_intersect_key($files, $data['entity']->getVars());
        } */
    }

    public function save($object)
    {
        $foreignKeys = $this->saveChildren($object/*, $files, $object*/);
        $class = ucfirst($object->getClass());
        $data = $object->getDataMapper()->getColumns();
        $changes = $object->getChanges();
        $data = array_intersect($data, $changes); // ajout des champs en fonction des changements suivis
        var_dump($data);
        foreach ($data as $prop => &$value) {

            $value = $object->$prop; // zjout de valeurs aux champs
        }
        $data = array_merge($data, $foreignKeys); // ajout  des clés étrangeres aux champs
       /* if ($object->getId()) {
            $data['id'] = $object->getId();
            $result = $this->getTable($class)->update(
                $data //, $files
            );
        } else {
            if(array_key_exists('date', $object->getVars())){
                $data->setDate($this->insertDate());
            }
			
            $result = $this->getTable($class)->create(
                $data // , $files
            );
        }

        if ($result) {
            $object->setId($this->getTable($class)->lastInsertId());
            /* Si c'est une inscription on logue le nouvel utilisateur */
            /*if (isset($data['login'])) {
                $id = $this->getTable($class)->lastInsertId();
                $_SESSION['auth'] = $id;
            }*/
        /*}

        return $result;*/
    }

    public function saveChildren(&$entity/*, &$files, $data*/)
    {
        $fkeys = array();
        $result = null;
        if (!$entity instanceof \Core\Entity\Entity) {
            throw new \Exception ("Form data not valid.");
        }

        $associationTypes = $entity->getDataMapper()->getAssociations();
        foreach ($associationTypes as $typeName => $type) {
            if ($fields = array_flip(array_intersect($entity->getChanges(), array_keys($type)))) {
                foreach ($fields as $prop => $child) {
                    // var_dump($prop);
                    // var_dump($child);
                    // envoyer uodate ou create avec le bon type de données
                    $get = 'get'.ucfirst($prop);
                    $class = $type[$prop]['targetEntity'];
                    $child = $entity->$get();
                    $fk = $type[$prop]['foreignKey']?  :$prop.'_id';
                    $data = $child->getVars();
                    $data = array_flip(array_intersect($data, $entity->getChanges()));
                    // compare sub object data TODO test

                    /* if(!$child->getId()) {
                        $result = $this->getTable($class)->create(
                            $data//, $childObject, $childFiles, $class
                        );
                    } else if ($child->getChanges()) {
                        $result = $this->getTable($class)->update(
                            $data//, $childObject, $childFiles, $class
                        );
                    }
                    if ($result || !$child->getChanges()) {
                        $id = $child->getId()? : $this->getTable($class)->lastInsertId();
                        $fkeys[$fk] = $id;
                    }*/
                }
            }
        }
        return $fkeys;

        /*   if (isset($data['children'])) {
            $children = $data['children'];
            foreach ($children as $class => $child) {
                $data = $child['entity'];
                // var_dump($object->getVars());
                if ($data) {
                    foreach ($data->getVars() as $key => $value) {
                        if (array_key_exists($key, $fields)) {
                            $childObject = array_intersect_key($fields, $data->getVars());
                            $fields = array_diff_key($fields, $data->getVars());
                        }
                        if (array_key_exists($key, $files)) {
                            $childFiles = array_intersect_key($files, $data->getVars());
                            $files = array_diff_key($files, $data->getVars());
                        }
                    }

                    if ($data->getId() === null) {
                        $result = $this->getTable($class)->create(
                            $data//, $childObject, $childFiles, $class
                        );
                        if ($result) {
                            $id =  $this->getTable($class)->lastInsertId();

                            $fields[$children[key($children)]['db_name']] = $id;
                        }
                    } else {
                        $result = $this->getTable($class)->update(
                            $data//, $childObject, $childFiles, $class
                        );
                    }
                }
            }
        } else {
            $fields = array_intersect_key($fields, $data['entity']->getVars());
            $files = array_intersect_key($files, $data['entity']->getVars());
        }*/
    }
}