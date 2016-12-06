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
    const ASSOCIATION_TYPES = 'ManyToOne_OneToOne_ManyToMany_OneToMany';

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
            $associationTypes = explode('_', self::ASSOCIATION_TYPES);
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
                                'targetEntity' => $association['targetEntity']
                            );
                            if (($type === 'ManyToOne' || $type = 'OneToOne') && $this->hasForeignKey($association)) {
                                $this->associationMappings[$type][$name]['foreignKey'] = $association['foreignKey']['name'];
                            } else if ($type === 'ManyToOne') {
                                $this->associationMappings[$type][$name]['foreignKey'] = $this->guessForeignKey($name);
                            }
                            if($this->hasForeignKey($association)) {
                                $this->columns[$association['foreignKey']['name']] = $name;
                                $this->fields[$name] = $association['foreignKey']['name'];
                                $this->foreignKeys[$association['foreignKey']['name']] = $association['foreignKey']['referencedColumnName'];
                            }
                        }
                        unset($metadata['properties'][$type]);
                    }
                }
            }

        }
    }

    public function hasForeignKey($association)
    {
        return isset($association['foreignKey']) && isset($association['foreignKey']['name']);
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
        if (array_key_exists($column, $this->columns)) {

            return $this->columns[$column];
        }

        return false;
    }

    public function getColumnFromProperty($property)
    {
        if (array_key_exists($property, $this->fields)) {

            return $this->fields[$property];
        }

        return false;
    }

    public function getPrimaryKey()
    {
        return $this->pkType;
    }

    public function getColumnNames($props)
    {
        return array_map(array($this, 'getColumnFromProperty'), array_keys($props));
    }

    public function getProperties($columns)
    {
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

    public function hasMany($property)
    {
        $isManyToMany = array_key_exists('ManyToMany', $this->associationMappings)? in_array($property, $this->associationMappings['ManyToMany']) : false;
        $isOneToMany = array_key_exists('OneToMany', $this->associationMappings)? in_array($property, $this->associationMappings['OneToMany']) : false;

        return $isManyToMany || $isOneToMany;
    }

    public function isAssociation($property)
    {
        $associationTypes = explode('_', self::ASSOCIATION_TYPES);
        foreach ($associationTypes as $type) {
            if (array_key_exists($property, $type )) {
                return true;
            }
        }

        return false;
    }


    public function guessColumnName($string)
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string));
    }

    public function guessForeignKey($name)
    {
        return $name.'_id';
    }

    public function guessReferencedColumnName()
    {
        return 'id';
    }

    public function beforePersist($fields)
    {
        return array_combine(array_keys($this->getColumns($fields)), array_values($fields));
    }

    public function hydrate($data, $class)
    {
        $fields = $this->getProperties($data);

        $data = array_combine($fields, array_values($data));
        $properties = array_intersect_key($data, $this->getFields());

        $associationTypes = $this->getAssociations();
        foreach ($associationTypes as $name => $associations) {
            if ($name === 'ManyToOne') {
                $data = array_intersect_key($data , $associations);

                foreach ($data as $prop => $value) {
                    $childClass = $associations[$prop]['targetEntity'];
                    if (!$value instanceof $childClass) {
                        $app = \App::getInstance();
                        // replacing foreign keys by entity
                        $properties[$prop] = $app->getTable($childClass)->find($value);
                    }
                }
            }
        }
        // now we can set all properties on new model

        $ref = new \ReflectionClass($class);
        $constructor = $ref->getConstructor();
        $arguments = $constructor->getParameters();
        $args = array();
        foreach($arguments as $key => $arg) {
            $args[$arg->name] = $key;
        }

        var_dump($args);
        $params = array_intersect_key($properties, $args);
        $properties  = array_diff_key($properties, $args);

        var_dump($params);

        $entity = new $class(extract($params));

        foreach ($properties as $prop => $value) {
            //var_dump($this->hasMany($prop));
            if ($this->hasMany($prop)) {
                $method = 'add'.ucfirst($prop);
            } else {
                $method = 'set'.ucfirst($prop);
            }
            $entity->$method($value);

        }
        return $entity;
    }

    public function hydrateAll($dataCollection, $class)
    {
        return array_map(function ($data) use ($class) {$this->hydrate($data, $class);}, $dataCollection);
    }
} 