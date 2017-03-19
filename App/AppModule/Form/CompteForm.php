<?php

namespace App\AppModule\Form;

use Core\Form\Form;
use Core\Form\FormBuilder;

class CompteForm extends Form
{
    public function getName()
    {
        return 'compte';
    }

    public function buildForm(FormBuilder $builder)
    {
        return $builder
            ->add('prenom', 'text', array(
                    'label' => 'Prénom:',
                    'labelType' => 'block',
                ))
            ->add('nom', 'text', array(
                    'label' => 'Nom:',
                    'labelType' => 'block',
                ))
            ->add('email', 'email', array(
                    'label' => 'Courriel:',
                    'labelType' => 'block',
                    'required' => true,
                    'unique' => true,
                ))
            ->add('mdp', 'password', array(
                'label' => 'Mot de Passe:',
                'labelType' => 'block',
                'doNotHydrate' => true,
            ))
            ->add('mdpConf', 'password', array(
                'label' => 'Mot de Passe:',
                'labelType' => 'block',
                'confirmation' => "mdp",
                'doNotHydrate' => true,
            ))
            ->add('role', 'entity', array(
                'data_class' => 'AppModule:Role',
                'label' => 'Role:',
                'placeholder' => 'Choisir...',
                'choice_name' => 'libelle',
                'labelType' => 'block',
                'parentTag' => array(
                    'div' => 'form-group'
                )
            ))
        ->add('action', 'hidden', array(
                'value' => 'editCompte'
            ))
        ->getForm();
    }

    public function setDefaultOptions()
    {
        return array(
            'enctype' => 'multipart/form-data',
        );
    }
}