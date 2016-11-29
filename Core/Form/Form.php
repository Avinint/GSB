<?php

namespace Core\Form;

abstract class Form{
	
	protected $data;
    protected $errors;
    protected $clicked;
    protected $fields = array();
    protected $entityLists = array();
    protected $options = array();
    protected $name;
    protected $parentForm = null;
	
	public function __construct($data = null, $action = null, array $lists = array())
	{
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
        return $this->data;
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

    public function validate($data)
    {
        $errors = array();
        foreach($this->fields as $key => $field)
        {
            /* if($field['type'] === 'text' || $field['type'] === 'textarea'){
                if(ctype_cntrl($data[$key]) || ctype_space($data[$key])){
                    $errors[$key]['textNotValid'] = 'Ce texte n\'est pas valide.';
                }

                // if($data[$key])
            }*/
            if(isset($key['options']['required']) && empty($data[$key])){
                $errors[$key]['emptyField'] = 'Ce champ ne peut pas être vide.';
            }

            if ($field['type'] === 'password') {
                if (!isset($field['options']['doNotHydrate'])) {
                    if (strlen($data[$key]) <3 || strlen($data[$key]) > 60) {
                        $errors[$key]['textNotValid'] = 'Ce texte n\'est pas valide.';
                    }
                }
                if(isset($field['options']['confirmation'])){
                    if($data[$key] !== $data[$field['options']['confirmation']]){
                        $errors['password']['confirmationError'] = 'Mot de passe non confirmé';
                    }
                }
            }

            /*if($key['options']['confirmation']){
                var_dump($key['options']['confirmation']);
                die();
            }*/
        }
           /* if($field['type'] === 'text'){
                if($field)
            }*/

        $this->errors = $errors;

        return empty($errors);
    }

	public function tag($html, $tag = 'div', $attr = [], $parent = null)
	{
		// Pour chaque attribut on rajoute le html, exemple: class="", et on les combine
		$attributes = []; $required = ''; $disabled ='';
		if($attr){
            $disabled = isset($attr['disabled'])? $attr['disabled']: '';
            if(isset($attr['disabled'])){unset($attr['disabled']);}
            $required = isset($attr['required'])? $attr['required']: '';
            if(isset($attr['required'])){unset($attr['required']);}
            foreach($attr as $k => $v){

                $attributes[$k] = empty($attr[$k])? '': ' '.$k.'="'.$v.'"';
            }
            $attributes = implode(' ', $attributes);
            $attributes = $attributes.$required.$disabled;
        }else{
            $attributes ='';
        }


        $result = '<'.$tag.$attributes.'>'.$html.'</'.$tag.'>';
        $result = $this->addParentTag($result, $parent);

		return $result;
	}

    public function addParentTag($html, $parent)
    {
        if($parent !== null){
            if(is_array($parent)){
                $attr = array();
                $attr['class'] = $parent[key($parent)];
                $parent = key($parent);
            }else{
                $attr = null;
            }
            $html = $this->tag($html, $parent, $attr);
        }
        return $html;
    }

    public function child(Form $form, $options)
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
        $name = $this->name.'_'.$name;

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


        }else if($type === 'submit'){
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
		}else{ // creation input simple

             if($type === 'password'|| isset($attributes['doNotHydrate'])){
                 $value ='';
             }else{
                 $value = array_key_exists('value',$attributes)? $attributes['value']:
                     $this->getValue($name);
             }
            $value = $value? ' value="'.$value.'"': '';
            $id = ' id="'.$id.'"';
            $name = ' name="'.$name.'"';
            if($min){
                $min = ' min='.$min;
            }
            if($max){
                $max = ' max='.$max;
            }
            if($type === 'choice'){
                if(isset($attributes['multiple']) && $attributes['multiple'] === true){

                    /** @TODO radio button */
                }else{
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
            }else{
                $input = '<input'.$class.' type="'.$type.'"'.$name.$id.$min.$max.$value
                    .$required.$disabled.'>';
            }

            $input  = $this->addParentTag($input, $fieldParent);
		}

        /* rendu du label selon type */
        if($labelType === 'empty'){
            $html = $input;
        }else if($labelType === 'after'){
            $html = $input.$label;
        }else{
            $html = $label.$input;
        }

        /* parent est une balise html qui entoure le champ du formulaire */
        if($parent){
            if(is_array($parent)){
                $parentName = key($parent);
                $parentClass = $parent[$parentName];
                if(is_numeric($parentName)){
                    throw new \Exception('Balise html tag non valide');
                }

                return $this->tag( // si hidden pas de label affiché
                    $html, $parentName, ['class' => $parentClass]
                    );
            }else{
                return $this->tag( // si hidden pas de label affiché
                   $html, $parent
                );
            }
        }else{
            return $html;
        }
	}

    public function select($name, $label, $options, $attributes = array())
    {
        $list = array();
        $parent = array_key_exists('parent', $attributes)?$attributes['parent']: null;
        unset($attributes['parent']);

       $class = isset($attributes['class'])? $attributes['class']: '';

        // Determine quel élément de la liste est selectionné par défaut
        foreach($options as $k => $v){
            $attr = array('value' => $k);
           ;
            if($k == $this->getValue($name)){
                $attr['selected'] = 'selected';
            }
            $list[] = $this->tag($v, 'option',$attr);

        }
        $html = implode(' ', $list);

        $label = $this->tag($label, 'label', ['for' => $name], $parent);
        $select = $label.$this->tag(
            $html,
            'select',[
                'class' => $class,
                'name'  => $name,
                'id' => $name,
            ]
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

    public function parseName($name)
    {
        $name = explode('_', $name);
        $name = array_pop($name);

        return $name;
    }

	public function getValue($name)
	{
        $name = $this->parseName($name);
		if(is_object($this->data)){
            //$method = 'get'.ucfirst($name);
            $data = $this->data->$name;

			return isset($data)? $data : null;
		}else{
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
                if(array_key_exists('list',$field['options'])) {
                    $list = $field['options']['list'];
                }else{
                    $list = $key;
                }
                $view.= $this->select(
                    $key,
                    $field['options']['label'],
                    $this->entityLists[$list],
                    $attributes
                );
            }else if($field['type'] === 'password'){
                $view.=$this->password(
                    $key,
                    $field['options']['label'],
                    $attributes
                );
            }else if($field['type'] === 'textarea'){

                $view.=$this->input(
                    $key,
                    $field['options']['label'],
                    $attributes
                );
            }else if($field['type'] === 'child'){
                $view.= $this->child(
                    $field['options']['form'],
                    $attributes
                );

            }else{
                $view.=$this->input(
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

    public function handleRequest($data)
    {
        if (!empty($_POST) || !empty($_FILES)) {

            $fields = $this->parseFields($_POST);
            $files = $this->parseFields($_FILES);
            $result = null;
			
            if ($this->validate($fields)) {
                if(isset($data['fk'])) {
                    foreach($data['fk'] as $k => $v){
                        if(array_key_exists($k, $fields)){
                            $fields[$v] = $fields[$k]; // $fields['role_id'] = $fields ['role']
                            unset($fields[$k]);  //unset role
                        }
                    }
                }
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
				
                $this->cascadeRequest($fields, $files, $data);
                
				$entity = $this->getData();
				$entity->getMetadata('fields');
                foreach ($fields as $attr => $value) {
                    $method = 'set'.ucfirst($attr);
                    $entity->$method($value);
                }

            } else {// fin validate
                echo 'formulaire non valide';
            }
        }
    }

    public function cascadeRequest(&$fields, &$files, $data)
    {
        $result = null;
        $childObject = array();
        $childFiles = array();

        if (isset($data['children'])) {
            $children = $data['children'];
            foreach ($children as $class => $child) {
                $obj = $child['entity'];
                // var_dump($object->getVars());
                if ($obj) {
                    foreach ($obj->getVars() as $key => $value) {
                        if (array_key_exists($key, $fields)) {
                            $childObject = array_intersect_key($fields, $obj->getVars());
                            $fields = array_diff_key($fields, $data->getVars());
                        }
                        if (array_key_exists($key, $files)) {
                            $childFiles = array_intersect_key($files, $obj->getVars());
                            $files = array_diff_key($files, $data->getVars());
                        }
                    }

                    /* if ($data->getId() === null) {
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
                    }*/
                }
            }
        } else {
            $fields = array_intersect_key($fields, $data['entity']->getVars());
            $files = array_intersect_key($files, $data['entity']->getVars());
        }
    }
}