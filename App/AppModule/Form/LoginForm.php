<?php

namespace App\AppModule\Form;

use Core\Form\Form;
use Core\Form\FormBuilder;

class LoginForm extends Form{

     public function getName()
    {
        return 'login';
    }

    public function buildForm(FormBuilder $builder)
    {
        return $builder
        ->add('pseudo', 'text', array(
            'label' => 'Pseudo:',
                'labelType' => 'block',
                    'required' => true,
                        'parentTag' => array(
                            'div' => 'form-group'
                    )
            ))
        ->add('mdp', 'password', array(
            'label' => 'Mot de Passe:',
                'labelType' => 'block',
                    'required' => true,
                        'parentTag' => array(
                            'div' => 'form-group'
                    )
            ))
            ->add('action', 'hidden', array(
                'value' => 'login'
            ))
        ->getForm();
    }
}