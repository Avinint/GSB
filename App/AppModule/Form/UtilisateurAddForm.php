<?php

namespace App\AppModule\Form;

use Core\Form\Form;
use Core\Form\FormBuilder;

class UtilisateurAddForm extends Form
{
    public function getName()
    {
        return 'uti';
    }

    public function getParent()
    {
        return new UtilisateurForm();
    }
    public function buildForm(FormBuilder $builder)
    {
        return $builder

        ->add('pseudo', 'text', array(
                    'label' => 'Pseudo:',
                    'labelType' => 'block',
                    'required' => true,
                    'unique' => true,
                    'parentTag' => array(
                        'div' => 'form-group'
                    )
                ))
        ->add('mdp', 'password', array(
            'label' => 'Mot de Passe:',
                'required' => true,
                    'labelType' => 'block',
                            'parentTag' => array(
                                'div' => 'form-group'
                            )
                ))


        ->getForm();
    }
}