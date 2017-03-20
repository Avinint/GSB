<?php

namespace Core\Component\DataBase;


use Core\Entity\Entity;

class UnitOfWork
{
    private $cleanObjects = array();
    private $dirtyObjects = array();
    private $changes = array();
    private $removedObjects = array();
    private $metadata;

/* structure des donness

objects = [
    ['data'] =>
        [oid =>
            [name => name, role_id => 2] // data
        ]  //identifier
    ]  // categories
*/


    // fill several data collections at once
    /*public function registerBatch(array $data)
    {
        foreach ($data as $object) {
            $this->register($object);
        }
    }*/



    // fill clean data collection
    public function register($data, $fqcn)
    {
        $data['fqcn'] = $fqcn;

        if ($this->exists($data, $this->dirtyObjects)) {
            return false;
        }
        if ($this->exists($data, $this->removedObjects)) {
            return false;
        }
        if ($this->exists($data, $this->cleanObjects)) {
            return false;
        }

        $this->add($data, $this->cleanObjects);

        return true;
    }

    // fill dirty data collection
    public function addChanges($entity)
    {
        $data = $entity->getVars();
        $class = $entity->getClass();

        $data['fqcn'] = $class;

        if ($this->exists($data, $this->cleanObjects)) {
            $this->add($data, $this->dirtyObjects);
        }
    }

    // add data to collection
    public function add($data, &$list)
    {
        $class = $data['fqcn'];
        $id = $data['id'];
        unset($data['fqcn']);
        if (!array_key_exists($class, $list)) {
            $list = array_merge(array($class => array()), $list);
        }

        $list[$class][$id] = $data;
    }

    // filter only the data that has changed
    public function setChanges($entity)
    {
        $data  = $entity->getVars();
        $class = $entity->getClass();
        $id    = $entity->getId();

        $this->addChanges($entity);
		if ($this->cleanObjects[$class]) {
			 $clean = $this->cleanObjects[$class][$id];
		}
       
		if ($this->dirtyObjects) {
			$dirty = $this->dirtyObjects[$class][$id];	
			$updated = array_diff_assoc($dirty, $clean);
			$this->changes[$class][$id] = $updated;	
		}
    }

    public function getChanges(Entity $entity)
    {
        $class = $entity->getClass();
        $id = $entity->getId();

        $this->setChanges($entity);
		
		if($this->changes) {
			return array_filter($this->changes[$class][$id]);
		}
        
		return null;
    }

    // check that data is present in a collection
    private function exists($data, $list)
    {
        $class  = $data['fqcn'];
        $id     = $data['id'];

        return  isset($list[$class]) && array_key_exists($id, $list[$class]);
    }

    // clear data from all collections
    public function clearAll()
    {
        $this->cleanObjects = array();
        $this->dirtyObjects = array();
        $this->removedObjects = array();
    }
} 