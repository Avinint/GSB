<?php

namespace Core\Component\Database;


class DataMapper
{
    private $fields = array();
    private $columns = array();
    private $fieldMappings = array();
    private $associationMappings = array();
    private $table = array();

    public function __construct($path)
    {
        $metadata = include $path;

        $this->table = $metadata['table'];
        $this->fields[key($metadata['properties'])] = $metadata['properties']['columns'];
        $this->column [key($metadata['properties']['columns'])] = $metadata['properties']['columns'];
        $this->fieldMappings = $metadata['properties'];
        $this->associationMappings['ManyToOne'] = isset($metadata['manyToOne']) ? : null;
        $this->associationMappings['OneToOne'] = isset($metadata['OneToOne']) ? : null;
        $this->associationMappings['OneToMany'] = isset($metadata['OneToMany']) ? : null;
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
} 