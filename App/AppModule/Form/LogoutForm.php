<?php

namespace App\AppModule\Form;

use Core\Form\Form;
use Core\Form\FormBuilder;

class LogoutForm extends Form{

     public function getName()
    {
        return 'logout';
    }

    public function buildForm(FormBuilder $builder)
    {
        return $builder

            ->add('action', 'hidden', array(
                'value' => 'logout'
            ))
        ->getForm();
    }
}