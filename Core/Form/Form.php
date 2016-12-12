<?php

namespace Core\Form;

use Core\Container\ContainerAware;
use Core\Entity\Entity;

abstract class Form extends ContainerAware{
	
	protected $data;
    protected $errors;
    protected $clicked;
    protected $fields = array();
    protected $entityLists = array();
    protected $options = array();
    protected $name;
    protected $parentForm = null;
    protected $container;
	
	public function __construct($data = null, $action = null, array $lists = array())
	{
        parent::__construct();
        if(!isset($this->options['action'])){
            $this->options['action'] ='';
        }
        if(!isset($this->options['enctype'])){
            $this->options['enctype'] ='';
        }
        if(!isset($this->options['method'])){
            $this->options['method'] ='post';
        }
        $this->fields = array_merge($this->fields, $this->buildForm(new FormBuilder($this)));
        $this->entityLists = $lists;
        $this->data = $data;
        $this->name = $this->getName();
        $options = $this->setDefaultOptions();
        if(!empty($options)){

            $this->setDefaults($options);
        }
        if(isset($action)){
            $this->options['action'] = $action;
        }
    }

    protected function setParentForm($parent)
    {
        $this->parentForm = $parent;
        $this->name = $parent.'_'.$this->getName();
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        if ($this->data instanceof Entity) {
            return $this->data;
        }

        return null;
    }

    public function getName()
    {
        return 'form';
    }

    public function getAction()
    {
        if(!isset($this->options['action'])){
            throw new \Exception('Action non configurée');
        }
        return $this->options['action'];
    }

    public function setDefaultOptions()
    {
        return array();
    }

    public function setDefaults($options = array())
    {
        $this->options['method'] = 'post';
        $this->options = array_merge($this->options, $options);
    }

    public abstract function buildForm(FormBuilder $builder);


    public function isValid()
    {
        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    private function isRequired($field)
    {
         return isset($field['options']['required']) && $field['options']['required'] === true;
    }

    public function validate($data)
    {
        $errors = array();
        //var_dump($this->fields);
        foreach($this->fields as $key => $field)
        {
            /* if($field['type'] === 'text' || $field['type'] === 'textarea'){
                if(ctype_cntrl($data[$key]) || ctype_space($data[$key])){
                    $errors[$key]['textNotValid'] = 'Ce texte n\'est pas valide.';
                }
            }*/
            if ($this->isRequired($field) && empty($data[$key])) {
                $errors[$key]['emptyField'] = 'Ce champ ne peut pas être vide.';
            }

            if ($field['type'] === 'password' && $this->isRequired($field)){

                if (isset($key, $data)) {
                    if (strlen($data[$key]) <3 || strlen($data[$key]) > 60) {

                        if (!isset($errors['password'])) {
                            $errors[] = array('password'=> array());
                        }
                        $errors['password'][] = array('textNotValid' =>'Ce texte n\'est pas valide.');
                    }
                }
            }

            if (isset($field['options']['confirmation'])) {
                if ($data[$key] !== $data[$field['options']['confirmation']]) {
                    $errors['password'][] = array('confirmationError' => 'Mot de passe non confirmé');
                }
            }

            if ($field['type'] === 'entity') {
                $entity = $this->getEntity($field['options']['data_class']);
                if($entity::hasDataMapper()) {

                    $fkType = $entity::dataMapper()->getPrimaryKey();
                    if(is_array($fkType)) {
                        $fkType = $fkType['type'];
                    }
                    if ($fkType === 'integer') {

                        if(!intval($data[$key]) < 0) {
                            throw new  \Exception("invalid foreign key");
                        }
                    }else if ($fkType === 'string') {
                        if(!is_string($data[$key])) {
                            throw new  \Exception("invalid foreign key");
                        }
                    }
                } else {
                    if((intval($data[$key])) < 1 && $this->isRequired($field)) {
                        throw new  \Exception(sprintf("invalid entity data: %s", $key));
                    }
                }
            }
        }
        $this->errors = $errors;

        return empty($errors);
    }

    private function getEntity($string)
    {
        $entity = explode (':', $string);
        $module = array_shift($entity);
        $className = array_shift($entity);

        return "App\\".$module.'\\Entity\\'.$className;
    }

    private function tag($html, $tag = 'div', $attr = [], $parent = null)
    {
        // Pour chaque attribut on rajoute le html, exemple: class="", et on les combine
        $attributes = []; $required = ''; $disabled ='';
        if ($attr) {
            $disabled = isset($attr['disabled'])? $attr['disabled']: '';
            if(isset($attr['disabled'])){unset($attr['disabled']);}
            //$required = isset($attr['required'])? $attr['required']: '';
           // if(isset($attr['required'])){unset($attr['required']);}

            foreach($attr as $k => $v){

                $attributes[$k] = empty($attr[$k])? '': ' '.$k.'="'.$v.'"';
            }

            $attributes = implode(' ', $attributes);
            $attributes = $attributes.$required.$disabled;

        } else {
            $attributes ='';
        }
        $result = '<'.$tag.$attributes.'>'.$html.'</'.$tag.'>';


        $result = $this->addParentTag($result, $parent);

        return $result;
    }

    public function addParentTag($html, $parent)
    {
        if($parent !== null){
            if (is_array($parent)) {
                $attr = array();
                $attr['class'] = $parent[key($parent)];
                $parent = key($parent);
            } else {
                $attr = null;
            }
            $html = $this->tag($html, $parent, $attr);

        }
        return $html;
    }

    private function child(Form $form, $options)
    {
        $parent = array_key_exists('parent', $options)? $options['parent']: null;
        $form->setParentForm($this->getName());
        $form->setData($this->data);
        $view = $form->buildView('');
        $view = $this->addParentTag($view, $parent);
        return $view;
    }

	public function input($name, $label, $attributes = array())
	{
        $parent = array_key_exists('parentTag', $attributes)?$attributes['parentTag']: null;
        $labelParent = array_key_exists('labelParent', $attributes)?$attributes['labelParent']: null;
        $fieldParent = array_key_exists('fieldParent', $attributes)?$attributes['fieldParent']: null;
		$type = isset($attributes['type'])? $attributes['type']: 'text';
		$class = isset($attributes['class'])? ' class="'.$attributes['class'].'"': '';
        $id =  isset($attributes['id'])? $attributes['id']: $this->name.'_'.$name.'_id';
        $min = isset($attributes['min'])? $attributes['min']: '';
        $max = isset($attributes['max'])? $attributes['max']: '';
        $disabled =  isset($attributes['disabled'])&& $attributes['disabled']? ' disabled': '';
        $required = (isset($attributes['required'])&& $attributes['required'])? ' required': '';
        $labelType = array_key_exists('labelType', $attributes)?$attributes['labelType']: null;
        unset($attributes['labelType']);

        if($type == 'submit' || $type == 'button'|| $type == 'hidden'){
            $labelType = 'empty';
        }

        if($labelType === 'empty'){
            $label = $this->tag($label, 'label', ['for' => $id], $labelParent);
        }else if($labelType === 'block'){
            $label = $this->tag($label, 'label', ['for' => $id], "div");
        }else{
            $label = $this->tag($label, 'label', ['for' => $id], $labelParent);
        }

        $button = $name; // On conserve le nom pour le  texte du bouton submit
        $name = $this->name.'['.$name.']';

        if($type === 'textarea'){ // creation input type textarea

            $input = $this->tag(
                $this->getValue($name),
                $type,
                array(
                    'name' => $name,
                    'id' => $id,
                    'class' => $class,
                    'disabled' => $disabled,
                    'required' => $required,
                ),
                $fieldParent
            );

        } else if ($type === 'submit') {
            $buttonId = isset($attributes['buttonId'])? $attributes['buttonId']: 'submit';
            $id = ' id="'.$this->name.'_'.$buttonId.'_id"';
            $input = '<input'.$class.' type="'.$type.'"'.$id.' value="'.$button
                .'"/>';

            $input  = $this->addParentTag($input, $fieldParent);

           /* if($fieldParent){
                if(is_array($fieldParent)){
                    $parentAttr = array();
                    $parentAttr['class'] = $fieldParent[key($fieldParent)];
                    $fieldParent = key($fieldParent);
                }else{
                    $parentAttr = null;
                }
                $input = $this->tag($input, $fieldParent, $parentAttr);
            }*/
        } else {   // creation input simple
             if ($type === 'password'|| isset($attributes['doNotHydrate'])) {
                 $value ='';
             } else {
                 $value = array_key_exists('value',$attributes)? $attributes['value']:
                     $this->getValue($name);
             }
            $value = $value? ' value="'.$value.'"': '';
            $id = ' id="'.$id.'"';
            if($min){
                $min = ' min='.$min;
            }
            if($max){
                $max = ' max='.$max;
            }
            if ($type === 'choice') {
                if (isset($attributes['multiple']) && $attributes['multiple'] === true) {
                    if (!isset($attributes['expanded']) || !$attributes['expanded'] === false) {
                        $name = ' name="'.$name.'[]"';
                    } else {
                        $name = ' name="'.$name.'"';
                        foreach($attributes['choices'] as $choice => $value){
                            $value = ' value="'.$value.'"';
                            $input = '<input'.$class.' type="radio"'.$name.$id.$min.$max.$value
                                .$required.$disabled.'>';
                            if($attributes['childLabelType'] === 'after'){
                                $input = $input.$choice.BR;
                            }else if($attributes['childLabelType'] === 'empty'){
                                /* do nothing */
                            }else{
                                $input = $choice.$input.BR;
                            }
                        }
                    }
                } else {
                    if (!isset($attributes['expanded']) || !$attributes['expanded'] === false) {
                        $name = ' name="'.$name.'[]"';
                    }else {
                        $name = ' name="'.$name.'"';
                        foreach($attributes['choices'] as $choice => $value){
                            $value = ' value="'.$value.'"';
                            $input = '<input'.$class.' type="checkbox"'.$name.$id.$min.$max.$value
                                .$required.$disabled.'>';
                            if($attributes['childLabelType'] === 'after'){
                                $input = $input.$choice.BR;
                            }else if($attributes['childLabelType'] === 'empty'){
                                /* do nothing */
                            }else{
                                $input = $choice.$input.BR;
                            }
                        }
                    }
                }
            } else {
                $name = ' name="'.$name.'"';
                $input = '<input'.$class.' type="'.$type.'"'.$name.$id.$min.$max.$value
                    .$required.$disabled.'>';
            }

            $input  = $this->addParentTag($input, $fieldParent);
        }

        /* rendu du label selon type */
        if ($labelType === 'empty' || $type == 'hidden') {
            $html = $input;
        } else if ($labelType === 'after') {
            $html = $input.$label;
        } else {
            $html = $label.$input;
        }

        /* parent est une balise html qui entoure le champ du formulaire */
        if ($parent) {
            if (is_array($parent)) {
                $parentName = key($parent);
                $parentClass = $parent[$parentName];
                if (is_numeric($parentName)) {
                    throw new \Exception('Balise html tag non valide');
                }

                return $this->tag(
                    $html, $parentName, ['class' => $parentClass]
                    );
            } else {
                return $this->tag(
                   $html, $parent
                );
            }
        }else{
            return $html;
        }
    }

    private function select($name, $label, $options, $attributes = array())
    {
        $list = array();
        $id = $this->getName().'_'.$name;
        $name = $this->getName().'['.$name.']';
        $class = isset($attributes['attr']) && isset($attributes['attr']['class'])? $attributes['attr']['class']: '';
        // Determine quel élément de la liste est selectionné par défaut
        $basicAttributes = array(
            'class' => $class,
            'name'  => $name,
            'id' => $id,
        );
        $attributes = array_merge($basicAttributes, $attributes);

        // Si options  non multiple et non required on ajoute un placeholder blanc
        if (isset($attributes['multiple']) &&  $attributes['multiple']) {
            $name.='[]';
            unset($attributes['multiple']);
        } else if(!isset($attr['required']) || !$attr['required']) {
            $emptyValue = isset($attributes['placeholder']) ? $attributes['placeholder'] : '';
            $noData = array(array('0', $emptyValue));
            $options = array_merge($noData, $options);
            unset ($attributes['placeholder']);
        }
        // on génère le choix
        foreach($options as &$option) {
            $value = array_shift($option);
            $choice = array_shift($option);
            $attr = array('value' => $value);

            if($value == $this->getValue($name)){

                $attr['selected'] = true;
            }

            $list[] = $this->tag($choice, 'option',$attr);
        }
        $html = implode(' ', $list);

        if (isset($attributes['multiple']) &&  $attributes['multiple']) {
            $name.='[]';
            unset($attributes['multiple']);
        }

        $forbidden = array('data_class', 'type', 'choice_name', 'choice_value');
            foreach ($forbidden as $remove) {
            if(array_key_exists($remove, $attributes)) {
                unset($attributes[$remove]);
            }
        }

        $parent = array_key_exists('parentTag', $attributes) ? $attributes['parentTag']: null;
        if($parent) {unset($attributes['parentTag']);}

        $labelParent = null;
        if (array_key_exists('labelType', $attributes)) {
            $labelType = $attributes['labelType'];
            unset($attributes['labelType']);
            if ($labelType === 'block') {
                $labelParent = 'div';
            } else {
                $labelParent = array ('div' => $labelType);
            }
        }
		
        $label = $this->tag($label, 'label', ['for' => $id], $labelParent);
        $select = $label.$this->tag(
            $html,
            'select',
            $attributes,
            $parent
        );
        return $select;
    }

    /*
     * Racourcis pour générer elements de formulaire: mot de passe, submit etc.
     *  en utilisant les fonctions locales
     */
    public function password($name, $label, $attributes = array())
    {
        $attributes['type'] = 'password';
        return $this->input($name, $label, $attributes);
    }

    public function submit($text = "envoyer", $class = 'btn btn-primary', $parent = null)
    {
        return $this->tag(

            $this->input($text, '', array(
                    'type' => 'submit',
                    'class' => $class,
                ),
                $parent
            )
           //'<input type="submit" class="btn btn-primary" value="'.'"/>'
        );
    }

    public function end($text ='', $class = '')
    {
        $html ='';
        if($class === ''){
            $html .= $this->submit($text);
        }else{
            $html .= $this->submit($text, $class);
        }
        $html.='</form>';
        return $html;
    }

   public function parseFields($names)
    {
        $keys =  array_map(array($this, 'parseName'),array_keys($names));
        $array = array_combine($keys, array_values($names));
        return $array;
    }

    private function parseName($name)
    {
        $name = rtrim($name,']');
        $name = explode('[', $name);
        $name = array_pop($name);

        return $name;
    }

    private function isEntity($name)
    {
        return isset($this->fields[$name]) && $this->fields[$name]['type'] === 'entity';
    }

    private function getIdentifier($name)
    {
        if (isset($this->fields[$name]['options']) && isset($this->fields[$name]['options']['choice_value'])) {
            return 'get'.ucfirst($this->fields[$name]['options']['choice_value']);
        }
        return 'getId';
    }

    public function getValue($name)
    {
        $name = $this->parseName($name);
        if (is_object($this->data)) {
            $method = 'get'.ucfirst($name);

            $data = $this->data->$method();
            if ($data && $this->isEntity($name)) {
                $id = $this->getIdentifier($name);
                $data = $data->$id();
            }
            return isset($data)? $data : null;
        } else {
            $data = $this->data[$name];
            return isset($data)? $data : null;
        }
    }

    public function all()
    {
        return $this->fields;
    }

    public function start($enctype = '', $action = '', $method = '')
    {
        if($action){
            $action = ' action="'.$action.'"?submit=true';
        }else if(isset($this->options['action'])){
            $action = ' action="'.$this->options['action'].'"?submit=true';
        } else{
            $action = ' ';
        }
        if($enctype){
            $enctype = ' enctype="'.$enctype.'"';
        }else if(isset($this->options['enctype'])){
            $enctype = ' enctype="'.$this->options['enctype'].'"';
        }else{
            $enctype ='';
        }
        if($method){
            $method = ' method="'.$method.'"';
        }else if(isset($this->options['method'])){
            $method = ' method="'.$this->options['method'].'"';
        }

        return '<form'.$method.$action.$enctype.'>';
    }

    public function buildView($view, $parent = array())
    {
        foreach($this->fields as $key => $field) {
            if($parent){
                array_push($field['options'], $parent);
            }
            // On inverse les optiosn label et type des attributs html
            $attributes = $field['options'];

            if(array_key_exists('label', $attributes)){
                unset($attributes['label']);
            }
            if(array_key_exists('list', $attributes)){
                unset($attributes['list']);
            }
            $attributes['type'] = $field['type'];
            if($field['type'] === 'select'){
                if (array_key_exists('list',$field['options'])) {
                    $list = $field['options']['list'];
                }else{
                    $list = $key;
                }
                $view .= $this->select(
                    $key,
                    $field['options']['label'],
                    $this->entityLists[$list],
                    $attributes
                );
            } else if ($field['type'] === 'entity') {
				$entity = $field['options']['data_class'];
				$table = $this->container['app']->getTable($entity);
				if(isset($field['options']['choice_value'])) {
					$choiceValue = $field['options']['choice_value'];
				} else {
					$choiceValue = 'id';
				}
				if (isset($field['options']['choice_name'])) {
					$choiceName = $field['options']['choice_name'];
				} else if (method_exists($this->data->$key, 'getName')) {
					$choiceName = 'name';
				} else if (method_exists($this->data->$key, 'getNom')) {
					$choiceName = 'nom';
				} else {
					$choiceName = $choiceValue;
				}
				$list = array();
                $orderBy = array('orderBy' => $choiceName);
				$extraction = $table->pluck($choiceValue, $choiceName, $orderBy);
                foreach ($extraction as &$res) {
                    if(count($res) === 1) {
                        $id = (string)$res['id'];
                        $list[$res['id']] = $res['id'];
                    } else {
                        $value = array_shift($res);
                        $value = (string)$value;

                        $name = array_shift($res);
                        $list[] = array(strval($value) , $name) ;
                    }
                }

                $view .= $this->select(
                    $key,
                    $field['options']['label'],
                    $list,
                    $attributes
                );
            } else if ($field['type'] === 'password') {
                $view .= $this->password(
                    $key,
                    $field['options']['label'],
                    $attributes
                );
            } else if ($field['type'] === 'textarea') {
                $view .= $this->input(
                    $key,
                    $field['options']['label'],
                    $attributes
                );
            } else if ($field['type'] === 'child') {
                $view .= $this->child(
                    $field['options']['form'],
                    $attributes
                );
            } else {
                $view .= $this->input(
                    $key,
                    $field['options']['label'],
                    $attributes
                );
            }
        }

        return $view;
    }

    public function createView(array $parameters = array())
    {
        $view = '';
        $parent = null;

        $this->options['enctype'] = isset($parameters['enctype'])? $parameters['enctype']: $this->options['enctype'];
        $this->options['action'] = isset($parameters['action'])? $parameters['action']: $this->options['action'];
        $this->options['method'] = isset($parameters['method'])? $parameters['method']: $this->options['method'];

        $view .= $this->start();
        $view .= $this->buildView($view, $parent);

        $view .= $this->end(array_key_exists('submit', $parameters)? $parameters['submit']: null);
        return $view;
    }

    public function redirect($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        die();
    }

    public function handleRequest()
    {

        if (!empty($_POST) || !empty($_FILES)) {
            $fields = array_shift($_POST);
            foreach ($fields as &$field) {
                $field = isset($field) ? $field : null;
            }

            $files = array_shift($_FILES);
            $result = null;

            if ($this->validate($fields)) {

                foreach ($this->all() as $name => $def) {
                    if ($def['type'] === 'password') {
                        if (isset($def['options']['confirmation']) || $fields[$name] === '') {
                            unset($fields[$name]);
                        }
                    }
                    if($def['type'] === 'hidden' && $name === 'action') {
                        unset($fields[$name]);
                    }
                }

                if ($entity = $this->getData()) {

                    $clone = clone $entity;
                    $entity::getTable()->setChanges($entity::getTable()->trackChanges($entity, $clone));
                   // var_dump($entity->getChanges());

                    $scalars = array_intersect_key($fields, $entity->getDataMapper()->getFields());
                    foreach ($scalars as $attr => $value) {
                        $method = 'set'.ucfirst($attr);
                        $entity->$method($value);
                    }

                    $entity::getTable()->setChanges($entity::getTable()->trackChanges($entity, $clone));
                    $this->cascadeRequest($fields, $files);
                }

            } else {// fin validate
                echo 'formulaire non valide';
            }
        }
    }

    public function cascadeRequest(&$fields, &$files)
    {
        $result = null;
        $entity = $this->data;
        if (!$this->data instanceof Entity) {
            throw new \Exception ("Form data not valid.");
        }
        $clone = clone $entity;

        //_dump($clone);
        $associationTypes = $this->data->getDataMapper()->getAssociations();
        foreach ($associationTypes as $type) {
            if ($fields = array_intersect_key($fields, $type)) {
                $this->cascadeAssociation($entity, $fields, $type);
            }
        }

        // one to one or array many to one relationships
       // $entity::getTable()->setChanges($entity::getTable()->trackChanges($entity, $clone));
        // many to many or one to many relationships
        //$entity::getTable()->setChanges($entity::getTable()->trackArrayChanges($entity, $clone));
    }

    public function cascadeAssociation($entity, $fields, $type)
    {
        foreach ($fields as $prop => $value) {
            $field = $this->fields[$prop];
            $multiple = isset($field['options']['multiple']) && $field['options']['multiple'];
            $set = 'set'.ucfirst($prop);
            $get = 'get'.ucfirst($prop);
            $add = 'add'.ucfirst($prop);
            $association = $type[$prop];
            $class = $association['targetEntity'];
            // on change la valeur si on a reneigné le champ  ou si les valeurs nulles sont admises non par défaut
            if ($value || isset($association['nullable']) && $association['nullable'] === true) {
                $child = is_object($value) ? $value : $this->container['app']->getTable($class)->find($value);
                if ($child instanceof $class || is_null($child)) {
                    $associationTypes = $child->getDataMapper()->getAssociations();
                    foreach ($associationTypes as $type) {
                        if ($fields = array_intersect_key($fields, $type)) {
                            $this->cascadeAssociation($entity, $fields, $type);
                        }
                    }

                    if (($type === "ManyToMany" || $type === "OneToMany") && $multiple && is_array($entity->$get())) {
                        $entity->$add($child);
                    } else {
                        $entity->$set($child);
                    }
                } else if (is_null($class)) {
                    $entity->$set(null);
                } else {
                    throw new \Exception("Entité invalide");
                }
            }
            // add HYDRATOR for update
        }
    }
}