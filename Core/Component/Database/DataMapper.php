<?php

namespace Core\Component\Database;


class DataMapper
{
    private $fields = array();
    private $columns = array();
    private $foreignKeys = array();
    private $fieldMappings = array();
    private $associationMappings = array();
    private $table;
    private $pkType;

    public function __construct($path)
    {
        $metadata = include $path;

        if (isset($metadata['table'])) {
            $this->table = $metadata['table'];
        }
        if (isset($metadata['primaryKey'])) {
            $this->pkType = $metadata['primaryKey'];
        }



        if (isset($metadata['properties'])) {
            $associationTypes = array('ManyToOne', 'OneToOne', 'ManyToMany', 'OneToMany');
            foreach($metadata['properties'] as $propertyName => $property) {
                if (!in_array($propertyName, $associationTypes)) {
                    $this->fieldMappings[$propertyName] =  $property;
                    if (isset($property['columnName'])) {
                        $this->fields[$propertyName] = $property['columnName'];
                        $this->columns[$property['columnName']] = $propertyName;
                    } else {
                        $column = $this->guessColumnName($propertyName);
                        $this->fields[$propertyName] = $column;
                        $this->columns[$column] = $propertyName;
                    }
                }
            }
            foreach ($metadata['properties'] as $propertyName => $property) {
                foreach ($associationTypes as $type) {

                    if(isset($metadata['properties'][$type])) {
                        foreach ($metadata['properties'][$type] as $name => $association) {
                            $this->associationMappings[$type][$name] = array(
                                'targetEntity' => $association['targetEntity'],
                                'foreignKey' => $association['foreignKey']['name']
                            );

                            $this->columns[$association['foreignKey']['name']] = $name;
                            $this->foreignKeys[$association['foreignKey']['name']] = $association['foreignKey']['referencedColumnName'];
                        }
                        unset($metadata['properties'][$type]);
                    }
                }
            }

        }
    }

    public function getAssociations($type = null)
    {
        if ($type) {
            return $this->associationMappings[$type];
        }

        return $this->associationMappings;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getPrimaryKeyType()
    {
        return $this->pkType;
    }

    public function getPropertyFromColumn($column)
    {
        $field = '';
        //var_dump(array_key_exists($column, $this->columns));
        if (array_key_exists($column, $this->columns)) {
            return $this->columns[$column];
        }

        return false;
    }

    public function getProperties(Array $columns)
    {
        //var_dump( array_map(array($this, 'getPropertyFromColumn'), $this->columns));
        return array_map(array($this, 'getPropertyFromColumn'), array_keys($columns));
    }

    public function hasTable()
    {
        return isset($this->table);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getFieldMappings()
    {
        return $this->fieldMappings;
    }

    public function getAssociationMappings()
    {
        return $this->associationMappings;
    }

    public function guessColumnName($string)
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string));
    }

    public function hydrate($data, $class)
    {
        //$keys = array_map('function', array_keys($data));
        $entity = new $class();
        var_dump($class);
        $fields = $this->getProperties($data);
        $data = array_combine($fields, array_values($data));

        $properties = array_intersect_key($data, $this->getFields());
        foreach ($properties as $prop => $value) {
            $set = 'set'.ucfirst($prop);
            $entity->$set($value);
        }
        $associationTypes = $this->getAssociations();
        foreach ($associationTypes as $name => $associations) {
            if ($name === 'ManyToOne') {
                var_dump('data');
                var_dump($data);
                var_dump('type');
                var_dump($name);
                var_dump($associations);
                $associations = array_intersect_key($data , $associations);

                var_dump(count($associations));
                foreach ($associations as $prop => $value) {

                    $class = $this->getFields();
                    $set = 'set'.$class;
                    $child = new $class();
                    var_dump('child');
                    var_dump($child);
                }
            }
        }
        $associations = array_intersect_key($data, $this->getFields());

        return $entity;
    }

    public function hydrateAll($dataCollection, $class)
    {
        return array_map(function ($data) use ($class) {$this->hydrate($data, $class);}, $dataCollection);
    }
} 