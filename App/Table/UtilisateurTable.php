<?php

namespace App\Table;

use \Core\Table\Table;

class UtilisateurTable extends Table
{
    public function findByUsername($username)
    {
        $query = $this->createQueryBuilder('u');
        $query
            ->where('u.pseudo = :pseudo')
            ->setParameter('pseudo', $username)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    public function findNoPassword($id)
    {
        $query = $this
            ->createQueryBuilder('u')
            ->select(array('u.id','u.pseudo', 'u.nom', 'u.prenom', 'u.email', 'u.image', 'u.role_id', 'u.newsletter'))
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

        return !$query->getSingleResult();
    }
}