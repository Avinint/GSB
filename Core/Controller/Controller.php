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
				var_dump($rule);
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

    public function save($entity)
    {
        $data = $entity->getVars();
        $class = $entity->getClass();

        $foreignKeys = $this->saveChildren($entity/*, $files, $entity*/);

        //$this->getTable($class)->setChanges($this->getTable($class)->trackChanges($entity, $clone));
       // $changes = $this->getTable($class)->getChanges();
       //$data = array_intersect($data, $changes); // ajout des champs en fonction des changements suivis


       // $data = array_merge($data, $foreignKeys); // ajout  des clés étrangeres aux champs
        if ($entity->getId()) {

            $data = $this->container['unit_of_work']->getChanges($entity);
            $data['id'] = $entity->getId();

            $result = $this->getTable($class)->update(
                $data //, $files
            );
        } else {
            if(array_key_exists('date', $entity->getVars())){
                $data->setDate($this->insertDate());
            }

            $result = $this->getTable($class)->create(
                $data // , $files
            );
        }
        if ($result) {

            $entity->setId($this->getTable($class)->lastInsertId());
        }

        return $result;
    }

    public function saveChildren(&$entity/*, &$files, $data*/)
    {
        $fkeys = array();
        $class = ucfirst($entity->getClass());
        $result = null;
        if (!$entity instanceof \Core\Entity\Entity) {
            throw new \Exception ("Form data not valid.");
        }

        $associationTypes = $entity->getDataMapper()->getAssociations();
        foreach ($associationTypes as  $association) {

            if ($fields = array_flip((array_keys($association)))) {

                foreach ($association as $prop => $info) {


                    // envoyer uodate ou create avec le bon type de données
                    $get = 'get'.ucfirst($prop);
                    $class = $association[$prop]['targetEntity'];
                    $child = $entity->$get();

                    if ($child) {
                        $data = $child->getVars();
                        $data = array_flip(array_intersect($data, $this->getTable($class)->getChanges()));
                        // compare sub object data TODO test

                        if(!$child->getId()) { // On insert objet si id non existent
                            $result = $this->getTable($class)->create(
                                $data//, $childObject, $childFiles, $class
                            );
                        }/* else { // ajouter unit of work
                        $result = $this->getTable($class)->update(
                            $data//, $childObject, $childFiles, $class
                        );
                    }*/

                        /*if ($result || !$child->getChanges()) {
                            $id = $child->getId()? : $this->getTable($class)->lastInsertId();

                        }*/
                        $id = $child->getId();
                        $fkeys[$prop] = $id;
                    }
                }
            }
        }
        return $fkeys;

        /*   if (isset($data['children'])) {
            $children = $data['children'];
            foreach ($children as $class => $child) {
                $data = $child['entity'];
                // var_dump($entity->getVars());
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