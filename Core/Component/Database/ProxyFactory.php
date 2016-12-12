<?php

namespace Core\Component\DataBase;

use Core\Entity\EntityFactory;

class ProxyFactory
{
    private $unitOfWork;
    private $dataMapper;

    public function __construct(DataMapper $dataMapper)
    {

        $this->dataMapper = $dataMapper;
        $this->unitOfWork = $dataMapper->getUnitOfWork();
    }

    public function create($fqcn, $id)
    {
        $proxy = new Proxy();
        $proxy->setFqcn($fqcn);
        $proxy->setId($id);

        $proxy->generateProxy();
    }

} 