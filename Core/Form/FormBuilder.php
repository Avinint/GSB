<?php 

namespace Core\Form;

class FormBuilder{

    private $children = array();
    protected $form;

	public function getForm(){
		return $this->children;
	}

    public function __construct(Form $form)
    {
        $this->form = $form;
        if(method_exists($form, 'getParent')){
            $parent = $this->form->getParent();
            $this->children = $parent->all();
        }
    }

    public function add($child, $type = 'text', array $options = array())
    {
        $this->children[$child] = null;
        $this->children[$child] = array(
            'type' => $type,
            'options' => $options,
        );
        if(!isset($this->children[$child]['options']['label'])){
            $this->children[$child]['options']['label'] = $child;
        }


        return $this;
    }

    public function remove($child)
    {

        unset($this->children[$child]);
        return $this;
    }

    public function get($child){
        if(isset($this->children[$child])){
            return $this->children[$child];
        }
        throw new \Exception(sprintf('Child %s does not exist', $child));
    }

    public function has($child)
    {
        if (isset($this->children[$child])) {
            return true;
        }
        return false;
    }

    public function all()
    {
        return $this->children;
    }

    public static function validateName($name)
    {
        if (null !== $name && !is_string($name) && !is_int($name)) {
            throw new  \Exception($name, 'string, integer or null');
        }

        if (!self::isValidName($name)) {
            throw new \Exception(sprintf(
                'The name "%s" contains illegal characters. Names should start with a letter, digit or underscore and only contain letters, digits, numbers, underscores ("_"), hyphens ("-") and colons (":").',
                $name
            ));
        }
    }

    public static function isValidName($name)
    {
        return '' === $name || null === $name || preg_match('/^[a-zA-Z0-9_][a-zA-Z0-9_\-:]*$/D', $name);
    }
}