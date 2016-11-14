<?php

namespace App\AppModule\Form;

use Core\Form\Form;
use Core\Form\FormBuilder;

class ProfilForm extends Form
{
    public function getName()
    {
        return 'profil';
    }

    public function buildForm(FormBuilder $builder)
    {
        return $builder
            ->add('prenom', 'text', array(
                    'label' => 'Prénom:',
                    'labelType' => 'block',
                    'required' => true,
                    'unique' => true,
                ))
            ->add('nom', 'text', array(
                    'label' => 'Nom:',
                    'labelType' => 'block',
                    'required' => true,
                    'unique' => true,
                ))
            ->add('image', 'file', array(
                    'label' => 'Avatar',
                    'parentTag' => array(
                        'div' => 'form-group'
                    ),
                    'labelType' => 'block',
                ))
            ->add('newsletter', 'checkbox', array(
                    'label' => 'Voulez vous recevoir notre newsletter?',
                    'choices' => array(
                        'Oui' => 1,
                        'Non' => 0,
                    ),
                    'parentTag' => array(
                        'div' => 'form-group'
                    ),
                    'labelType' => 'block',
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
            ->add('action', 'hidden', array(
                    'value' => 'editProfil'
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