<?php

namespace App\AppModule\Table;

use Core\Table\Table;

class UtilisateurTable extends Table
{
    public function findByUsername($username)
    {
        $query = $this->createQueryBuilder('u');
        $query
            ->where('u.login = :login')
            ->setParameter('login', $username)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    public function findNoPassword($id)
    {
        $query = $this
            ->createQueryBuilder('u')
            ->select(array('u.id','u.login', 'u.nom', 'u.prenom', 'u.email', 'u.image', 'u.role'))
            ->where('id = :id')
            ->setParameter('id', $id)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    public function valueAvailable($field, $value)
    {
        $query = $this->createQueryBuilder('u');
        $query
            ->select('u.'.$field)
            ->where('u.'.$field.' = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ;

        return false === $query->getSingleResult();
    }
}