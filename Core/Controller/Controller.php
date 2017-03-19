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

    protected function render($view, $variables = array(), $template = 'default')
    {
        $variables = array_merge($variables, $this->userData());
        $view = new View($view);
        $view->render($variables, $template);
    }

    /* rendu dans une variable */
    protected function renderView($view, $parameters)
    {
        ob_start();
        extract($parameters);
        require $view;

        return ob_get_clean();
    }

    protected function userData()
    {
        if (isset($_SESSION['user']))
        {
            $user = unserialize($_SESSION['user']);
            $role = $user->getRole()->getLibelle();
            return (array(
                'nom'    => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'login'  => $user->getLogin(),
                'activeRole'   => ucfirst($role)));
        }

        return (array('login' => 'undefined'));
    }

    public function getUser()
    {
        if (isset($_SESSION['user'])) {
            return unserialize($_SESSION['user']);
        }

        return false;
    }

    protected function partial($view, $vars)
    {
        $view = str_replace('/', D_S, $view);
        extract($vars);
        require 'views'.D_S.$view;
    }
	
	protected function generateURL($routeName, $parameters = array())
	{
		return $this->container['router']->generateURL($routeName, $parameters);
	}

    // transforme un snakecase en phrase avec espaces
    protected function humanize($string)
    {
        return ucfirst(str_replace('_', ' ', $string));
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
        //$this->redirect('?action=error&id=4');
        header('HTTP/1.0 403 Forbidden');
        die($msg);
    }

    protected function notFound()
    {
        //$this->redirect('?action=error&id=2');
        header('HTTP/1.0 404 Not Found');
        die('Page introuvable');
    }

    public function redirect($url)
    {
        //$url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].$url;
        header('Location: ' . $url);
        die();
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
       ## var_dump($associationTypes); ICI GET CHANGE PETE TOUT
        foreach ($associationTypes as $typeName => $type) {
          #var_dump(array_flip(array_intersect($this->getTable($class)->getChanges(), array_keys($type))));
            if ($fields = array_flip(array_intersect($this->getTable($class)->getChanges(), array_keys($type)))) {

                foreach ($fields as $prop => $child) {


                    // envoyer uodate ou create avec le bon type de données
                    $get = 'get'.ucfirst($prop);
                    $class = $type[$prop]['targetEntity'];
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




    // validation de formulaire retourne un message d'erreur pour chaque champ danss liste qui est vide
    protected function validateBlank($list)
    {
        $errors = array();
        $fields = $_POST;
        $emptyMsg = 'champ obligatoire';
        foreach ($fields as $field => $value) {
            if(empty($value) &&  in_array($field, $list)) {
                $errors[$field] = array();
                $errors[$field][] = $emptyMsg;
            }
        }

        return $errors;
    }

    protected function validateUniques($list, $user = null)
    {
        $fields = $_POST;
        $errors = array();
        foreach ($list as $field) {
            $method = 'findOneBy'.ucfirst($field);
            $userExists = Utilisateur::$method($fields[$field]);
            if(($userExists && !$user) || ($userExists && $userExists != $user)) {
                $errors[$field]   = array();
                $errors[$field][] = $this->humanize($field). " déja utilisé : veuillez en choisir un autre";
            };
        }

        /*var_dump($loginExists && !$user || $loginExists != $user);
        $emailExists = Utilisateur::findOneByEmail($fields['email']);
        var_dump($emailExists && !$user || $emailExists != $user);*/

        return $errors;
    }

    // validation de formulaire retourne un message d'erreur pour chaque champ danss liste qui est vide
    protected function validatePasswordConfirmation()
    {
        $errors = array();
        $fields = $_POST;
        if (isset($fields['mdp']) && isset($fields['mdp_confirmation'])) {

            if ($fields['mdp'] !== $fields['mdp_confirmation']) {
                $errors = array('mdp' => array());
                $errors['mdp'][] = 'Le mot de passe doit être identique dans le champ de confirmation';
            }
        }

        return $errors;
    }
}