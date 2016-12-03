<?php

namespace App\AppModule\Entity;

use Core\Entity\Entity;

class Pays extends Entity {

    protected $id;
    protected $nom;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }


}