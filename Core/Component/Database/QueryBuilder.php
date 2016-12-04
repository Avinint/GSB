<?php

namespace Core\Component\Database;

use Core\Table\Table;

class QueryBuilder{

    private $aliases = array();

	private $sqlParts = array(
        'distinct' => false,
        'select'   => array(),
        'from'     => array(),
        'join'     => array(),
        'set'      => array(),
        'where'    => null,
        'groupBy'  => array(),
        'having'   => null,
        'orderBy'  => array()
    );
    private $parentTable;
    private $repository;
    private $query;
    private $joins = array();
    private $parameters = array();
    private $start;
    private $count;

    public function __construct(Table $repository)
    {
        $this->repository = $repository;
    }

    private function getPrefix()
    {
        return $this->repository->getPrefix();
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function getSqlPart($key)
    {
        return $this->sqlParts[$key];
    }

	public function getAliases()
	{
		return $this->aliases;
	}

    public function addAlias($alias, $col)
    {
        $this->aliases[$alias] = $col;
       // $this->parentTable = $table;
    }

    // select remet à zero les champs selectionnés
	public function select($selection = '*')
	{
        $selection = is_array($selection) ? $selection : func_get_args();

         /*foreach ($selection as $prop) {
            $this->parseSelect($prop);
        }*/

        $this->sqlParts['select'] = $selection;

		//$this->fields = func_get_args();
		return $this;
	}

    public  function parseSelect($prop)
    {
        $separator = strpos($prop, '.');
        if($separator !== false)  {
            $select = explode('.', $prop);
            $oolumnName = array_pop($select);
            $meta = $this->repository->getEntity()->getDataMapper();
            $columnName = $meta->getColumnFromProperty($oolumnName);
            $alias = array_shift($select);
            $this->addAlias($alias, $columnName);
        }
    }

    public function distinct($flag = true)
    {
        $this->sqlParts['distinct'] = (bool) $flag;

        return $this;
    }

    // addSelect ajoute des champs à la selection
    public function addSelect($selection)
    {
        $selection = is_array($selection) ? $selection : func_get_args();
        /* si c'est un alias on le recupere dans la liste */
        if(!is_array($selection)) {
            $this->sqlParts['select'][] =  $selection; //

            return $this;

        }else{
            $this->sqlParts['select'] = array_flip(array_flip(array_merge( $this->sqlParts['select'], $selection)));
        }

        return $this;
    }

	public function where($where, $type = '')
	{
        if ($type) {
            $where = array('type' => $type, 'criteria' =>  $where);
        } else {
            $this->sqlParts['where'] = array(); // on vide le tableau pour premier where
        }
        $this->sqlParts['where'][] = $where;

		return $this;
	}
	
	public function andWhere($condition)
	{
        return $this->where($condition, 'AND');
    }

    public function orWhere($condition)
    {
        return $this->where($condition, 'OR');
    }

    public function from($table, $alias = null)
    {
        $alias = $alias ? : strtolower($table[0]);
        $from['table'] = $table;
        $from['alias'] = $alias;
        $this->addAliasKey($alias);
        $this->sqlParts['from'] = array();
        $this->sqlParts['from'][] = $from;

        return $this;
	}

    public function addFrom($table, $alias = null)
    {
        $alias = $alias ? : strtolower($table[0]);

        $from['table'] = $table;
        $from['alias'] = $alias;
        $this->addAliasKey($alias);
        $this->sqlParts['from'][] = $from;

        return $this;
    }

    public function addAliasKey($alias)
    {
        if(!array_key_exists($alias, $this->aliases)) {
            $this->aliases[$alias] = array();
        }
    }

    public function setParameter($identifier, $value)
    {
        $this->parameters[trim($identifier, ':')] = $value;

        return $this;
    }

    public function orderBy($field, $order = null)
    {
        $orderBy = array();
        $orderBy['field'] = $field;
        if($order) {
            $orderBy['order'] = $order;
        }

        $this->sqlParts['orderBy'] = $orderBy;
        //$this->sortBy = ' ORDER BY '.$field.' '.$mode;

        return $this;
    }

    public function limit ($start = 0, $count = 0)
    {
        if ($start) {
            $this->start = $start;
        }
        $this->count = $count;

        return $this;
    }

    public function count ($count)
    {
        $this->count = $count;

        return $this;
    }

    public function start ($start)
    {
        $this->start = $start;

        return $this;
    }

    public function leftJoin($join, $alias, $conditionType = 'ON', $condition = null)
    {
        return $this->setJoin('LEFT', $join, $alias, $conditionType, $condition);
    }

    public function join($join, $alias, $conditionType = 'ON', $condition = null)
    {
        return $this->setJoin('INNER', $join, $alias, $conditionType, $condition);
    }

    public function rightJoin($join, $alias, $conditionType = 'ON', $condition = null)
    {
        return $this->setJoin('RIGHT', $join, $alias, $conditionType, $condition);;
    }

    private function setJoin($type, $join, $alias, $conditionType = 'ON', $condition = null)
    {
        $parentAlias = substr($join, 0, strpos($join, '.'));

        $rootAlias = $this->findRootAlias($alias, $parentAlias);

        // TODO replace $join.'_id' par un appel à un data mapper
        $condition = $condition ? : $join.'_id = '.$alias.'.id';

        $join = array(
            'type'          => $type,
            'table'         => $this->parse_table($join),
            'alias'         => $alias,
            'conditionType' => $conditionType,
            'condition'     => $condition
        );
        $this->sqlParts['join'][$rootAlias][$parentAlias] = $join;

        return $this;
    }

    public function getJoin($alias)
    {
        $sql = '';
        if (isset($this->getSqlPart('join')[$alias])) {
            foreach($this->getSqlPart('join')[$alias] as $join) {
                $join = strtoupper($join['type']).' JOIN '.$this->getPrefix().$join['table'].' '.$join['alias'].' '
                    .(isset($join['conditionType'])? $join['conditionType']
                    :'ON').' '.$join['condition'].' ';
                $sql .= $join;
            }
        }

        return $sql;
    }

    private function findRootAlias($alias, $parentAlias)
    {
        $rootAlias = null;

        if (in_array($parentAlias, $this->getRootAliases())) {
            $rootAlias = $parentAlias;
        } elseif (isset($this->aliases[$parentAlias])) {
            $rootAlias = $this->aliases[$parentAlias];
        } else {
            $rootAlias = $this->$this->getRootAliases()[0];
        }

        $this->aliases[$alias] = $rootAlias;

        return $rootAlias;
    }

    public function getRootAliases()
    {
        $aliases = array();
        foreach ($this->sqlParts['from'] as $from) {
            $aliases[] = $from['alias'];
        }

        return $aliases;
    }

    private function writeSelect()
    {
        return implode(', ', array_map( array($this, 'getSelect'), $this->getSqlPart('select')));
    }

    private function getSelect($select)
    {
        return strpos($select, '.') === false && array_key_exists($select,$this->aliases) ? $select.'.*': $select;
    }

    private function writePart($partName, $options = array())
    {
        $queryPart = $this->getSqlPart($partName);

        if (empty($queryPart)) {
            return (isset($options['empty']) ? $options['empty'] : '');
        }
        if (is_array($queryPart)) {
            foreach ($queryPart as &$part){
                if(self::is_assoc($part) && isset($part['criteria'])){
                    $part =  implode( ' ',$part);
                    $options['separator'] = ' ';
                }
            }
        }
        $separator = isset($options['separator'])&& $options['separator'] ? $options['separator'] : ', ';

        return (isset($options['left']) ? $options['left'] : '')
        .(is_array($queryPart) ? implode($separator, $queryPart) : $queryPart)
        .(isset($options['right']) ? $options['right'] : '');
    }

    private function writeFrom()
    {
        $sql = '';

        $fromParts = $this->getSqlPart('from');
        $joinParts = $this->getSqlPart('join');
        $fromClauses = array();

        if (!empty($fromParts)) {
            $sql .= ' FROM ';

            foreach ($fromParts as $from) {
                $fromClause = $this->getPrefix().$from['table'].' '.$from['alias'];

                if (self::is_assoc($from) && isset($joinParts[$from['alias']])) {

                    foreach ($this->getSqlPart('join') as $join) {
                        $fromClause .= ' ' . $this->getJoin($from['alias']);
                    }
                }
                $fromClauses[] = $fromClause;
            }
        }
        $sql .= implode(', ', $fromClauses);

        return $sql;
    }

    private function writeEnd()
    {
        $sql = $this->writePart('where', array('left' => ' WHERE '))
        . $this->writePart('groupBy', array('left' => ' GROUP BY ', 'separator' => ', '))
        . $this->writePart('having', array('left' => ' HAVING '))
        . $this->writePart('orderBy', array('left' => ' ORDER BY ', 'separator' => ', '))
        . $this->getLimit();
        ;

        return $sql;
    }

    private function getLimit()
    {
        if (isset($this->count)) {
            $sql = ' LIMIT ';
            if (isset($this-> start)) {
                $sql .= $this->start.', '.$this->count;
            }else{
                $sql .= $this->count;
            }

            return $sql;
        }

        return '';
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getParameter($key, $value)
    {
        return array(':'.$key => $value);
    }

    public function getQuery()
    {
        $this->query = 'SELECT '
            . ($this->getSqlPart('distinct') === true ? ' DISTINCT' : '')
            . $this->writeSelect()
            . $this->writeFrom().$this->writeEnd();
        ;

        return $this;
    }

    public function getResults()
    {
        return $this->repository->query($this->query, $this->getParameters(), false);
    }

    public function getSingleResult()
    {
        return $this->repository->query($this->query, $this->getParameters(), true);

    }

    public function getSingleScalarResult()
    {

    }

    public function readSql()
    {
        echo 'SELECT '
            . ($this->getSqlPart('distinct') === true ? ' DISTINCT' : '')
            . $this->writeSelect()
            . $this->writeFrom().$this->writeEnd();
        ;
        return $this;
    }

    private static function is_assoc($array)
    {
        if (!is_array($array)) {
            return false;
        }
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

   private function parse_table($join)
   {
       $table = explode('.', $join);

       return array_pop($table);
   }
}