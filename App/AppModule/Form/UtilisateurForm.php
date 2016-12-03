<?php

namespace App\AppModule\Form;

use Core\Form\Form;
use Core\Form\FormBuilder;

class UtilisateurForm extends Form
{
    public function getName()
    {
        return 'uti';
    }

    public function buildForm(FormBuilder $builder)
    {
        return $builder
        ->add('pseudo', 'text', array(
            'label' => 'Pseudo:',
                'labelType' => 'block',
                    'required' => true,
                        'unique' => true,
                            'disabled' => true,
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
            ->add('prenom', 'text', array(
                    'label' => 'Prénom:',
                    'labelType' => 'block',
                    'unique' => true,
                ))
            ->add('nom', 'text', array(
                    'label' => 'Nom:',
                    'labelType' => 'block',
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
                'doNotHydrate' => true,
                    'labelType' => 'block',
                            'parentTag' => array(
                                'div' => 'form-group'
                            )
                ))
        ->add('mdpConf', 'password', array(
                'label' => 'Confirmer:',
                    'confirmation' => 'mdp',
                        'doNotHydrate' => true,
                            'labelType' => 'block',
                                    'parentTag' => array(
                                        'div' => 'form-group'
                                    )
                ))
            ->add('role', 'entity', array(
                    'class' => 'AppModule:Role',
                    'label' => 'Role:',
                    'labelType' => 'block',
                    'required' => true,
                    'parentTag' => array(
                        'div' => 'form-group'
                    )
                ))
            ->add('action', 'hidden', array(
                    'value' => 'uti'
                ))
        ->getForm();
    }
}