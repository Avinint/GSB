<?php

namespace Core\Form;

class BootstrapForm extends Form{
	
	
	
	public function select($name, $label, $options, array $attributes = array())
	{
		$list = [];
		$attr = [];
		
		foreach($options as $k => $v){
			$attr = array('value' => $k);
			if($k == $this->getValue($name)){
				$attr['selected'] = 'selected';
			}
			$list[] = $this->tag($v, 'option',$attr);
			
		}
		$html = implode(' ', $list);
        if(array_key_exists('class', $attributes)){
            $attributes['class'] = $attributes['class'].' form-options';
        }else{
            $attributes['class'] = 'form-options';
        }
		
		$label = $this->tag($label, 'label', ['for' => $name]); 
		$select = $this->tag(
			$html,
			'select',[
			'class' => 'form-options',
			'name'  => $name,
			'id' => $name,
			]
		);
		return $select;
	}
	
	public function password($name, $label, $options = array())
	{
		$options['type'] = 'password';
		return $this->input($name, $label, $options);
	}
	

	
	
}

