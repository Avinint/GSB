<?php

namespace App\Form;

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
        ->add('pseudo', 'text', array(
            'label' => 'Pseudo:',
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
            ->add('action', 'hidden', array(
                    'value' => 'signup'
                ))
        ->getForm();
    }
}