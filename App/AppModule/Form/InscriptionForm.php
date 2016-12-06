<?php

namespace App\AppModule\Form;

use Core\Form\Form;
use Core\Form\FormBuilder;

class InscriptionForm extends Form
{
    public function getName()
    {
        return 'signup';
    }

    public function buildForm(FormBuilder $builder)
    {
        return $builder
        ->add('login', 'text', array(
            'label' => 'Login:',
                'labelType' => 'block',
                    'required' => true,
                        'unique' => true,
                            'parentTag' => array(
                                'div' => 'form-group'
                    )
            ))
            ->add('email', 'email', array(
                    'label' => 'Email:',
                        'labelType' => 'block',
                            'required' => true,
                                'unique' => true,
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
        ->add('mdpConf', 'password', array(
                'label' => 'Mot de Passe:',
                    'confirmation' => 'mdp',
                        'labelType' => 'block',
                            'required' => true,
                                'parentTag' => array(
                                    'div' => 'form-group'
                                )
            ))
            ->add('role', 'entity', array(
                    'class' => 'AppModule:Role',
                    'label' => 'Role:',
                    'labelType' => 'block',
                    'required' => false,
                    'parentTag' => array(
                        'div' => 'form-group'
                    )
                ))
            ->add('pays', 'entity', array(
                    'class' => 'AppModule:Pays',
                    'label' => 'Pays:',
                    'labelType' => 'block',
                    'multiple' => false,
                    'required' => true,
                    'parentTag' => array(
                        'div' => 'form-group'
                    )
                ))
            ->add('action', 'hidden', array(
                    'value' => 'signup'
                ))
        ->getForm();
    }
}