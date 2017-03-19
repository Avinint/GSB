<?php
namespace Core\Entity;


class EntityFactory
{
    private $fqcn;
    private $data = array();


    public function setFqcn($fqcn)
    {
        $this->fqcn = $fqcn;
    }

    public function create()
    {
        $fqcn = $this->fqcn;
        $entity = new $fqcn();
    }

    public function setProperties()
    {

    }
}
