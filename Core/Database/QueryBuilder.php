<?php

namespace Core\Database;

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
        $this->sqlParts['select'] = $selection;

		//$this->fields = func_get_args();
		return $this;
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
        $this->sqlParts['from'] = array();
        $this->sqlParts['from'][] = $from;

        return $this;
	}

    public function addFrom($table, $alias = null)
    {
        $alias = $alias ? : strtolower($table[0]);

        $from['table'] = $table;
        $from['alias'] = $alias;
        $this->sqlParts['from'][] = $from;

        return $this;
    }

    public function setParameter($identifier, $value)
    {
        $this->parameters[trim($identifier, ':')] = $value;

        return $this;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getParameter($key, $value)
    {
        return array(':'.$key => $value);
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

    public function leftJoin($join, $alias, $joinTable = null)
    {
        $table = $joinTable ? $this->getPrefix().$joinTable :
            $this->getPrefix().$this->parse_table($join);
         // si la foreign key n'a pas le même nom que la table de jointure

        $join = ' LEFT JOIN '.$table. ' AS ' .$alias.' ON '.$alias.'.id = '.$join.'_id';
        $this->joins[$alias] = $join;
        $this->addAlias($alias, $table);

        return $this;
    }

    public function rightJoin($join, $alias, $joinTable = null)
    {
        $table = $joinTable? $this->getPrefix().$joinTable:
            $this->getPrefix().$this->parse_table($join);
        // si la foreign key n'a pas le même nom que la table de jointure

        $join = ' RIGHT JOIN '.$table. ' AS ' .$alias.' ON '.$alias.'.id = '.$join.'_id';
        $this->joins[$alias] = $join;
        $this->addAlias($alias, $table);

        return $this;
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

    private function writeSelect()
    {
        //var_dump(' '.implode(', ', $this->getSqlPart('select')));
        return implode(', ', array_map( array($this, 'getSelect'), $this->getSqlPart('select')));
    }

    private function getSelect($select)
    {
        return strpos($select, '.') === false? $select.'.*': $select;
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
                $fromClause = $from['table'].' '.$from['alias'];
                if (self::is_assoc($from) && isset($joinParts[$from['alias']])) {

                    foreach ($joinParts[$from['alias']] as $join) {
                        $fromClause .= ' ' . ((string) $join);
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
        . $this->writePart('orderBy', array('left' => ' ORDER BY ', 'separator' => ', '));

        return $sql;
    }

	private static function is_assoc($array)
	{
        if (!is_array($array)) {
            return false;
        }
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}

    public function checkQuery()
    {
        return $this->query;
    }

    public function getResults()
    {
        return $this->repository->query($this->query, $this->getParameters(), false);
    }

    public function getSingleResult()
    {
        return $this->repository->query($this->query, $this->getParameters(), true);
    }

   private function parse_table($join)
   {
       $table = explode('.', $join);

       return array_pop($table);
   }
}