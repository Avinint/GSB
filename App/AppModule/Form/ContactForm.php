<?php

namespace App\AppModule\Form;

use Core\Form\Form;
use Core\Form\FormBuilder;

class ContactForm extends Form{

     public function getName()
    {
        return 'login';
    }

    public function buildForm(FormBuilder $builder)
    {
        return $builder
        ->add('email', 'email', array(
            'label' => 'Email:',
                'labelType' => 'block',
                    'required' => true,
                        'parentTag' => array(
                            'div' => 'form-group'
                    )
            ))
            ->add('titre', 'text', array(
                    'label' => 'Titre:',
                    'labelType' => 'block',
                    'required' => true,
                    'parentTag' => array(
                        'div' => 'form-group'
                    )
                ))
        ->add('message', 'textarea', array(
            'label' => 'Message:',
                'labelType' => 'block',
                    'required' => true,
                        'parentTag' => array(
                            'div' => 'form-group'
                    )
            ))
        ->getForm();
    }

}