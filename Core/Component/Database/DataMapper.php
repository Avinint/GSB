<?php

namespace Core\Component\Database;


class DataMapper
{
    private $fields = array();
    private $columns = array();
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
            foreach ($metadata['properties'] as $propertyName => $property) {
                foreach ($associationTypes as $type) {

                    if(isset($metadata['properties'][$type])) {
                        foreach ($metadata['properties'][$type] as $name => $association) {
                            $this->associationMappings[$type][$name] = $association;
                        }
                        unset($metadata['properties'][$type]);
                    }
                }
            }

            foreach($metadata['properties'] as $propertyName => $property) {

                $this->fieldMappings[$propertyName] = $property;
                unset($this->fieldMappings[$propertyName]['columnName']);

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
} 