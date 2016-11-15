<?php

namespace Core\Database;

use Core\Table\Table;

class QueryBuilder{
	
	private $fields = array();
    private $aliases = array();
	private $tables = array();
	private $sqlParts = array(
        'distinct' => false,
        'select'  => array(),
        'from'    => array(),
        'join'    => array(),
        'set'     => array(),
        'where'   => null,
        'groupBy' => array(),
        'having'  => null,
        'orderBy' => array()
    );
    private $parentTable;
	private $condition;
    private $repository;
    private $query;
    private $sortBy;
    private $joins = array();
    private $parameters = array();
    private $criterias = array();
	

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
        var_dump($this->getPrefix());
        return $this->repository;
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
        if(is_array($selection)){
            $this->sqlParts['select'] =  $selection;
       }else{
            $this->sqlParts['select'] = array();
            $this->sqlParts['select'][]= $selection;
        }
		//$this->fields = func_get_args();
		return $this;
	}

    // addSelect ajoute des champs à la selection
    public function addSelect($selection, $alias = null)
    {
        /* si c'est un alias on le recupere dans la liste */
        if(!is_array($selection) && array_key_exists($selection, $this->aliases)){
            $selection = $this->aliases[$selection];
            $aliases = array_keys($this->aliases, $selection);
            $this->sqlParts['select'] = array_shift($aliases).'.*'; //
            return $this;

        }else if(is_array($selection)){
            $this->sqlParts['select'] = array_flip(array_flip(array_merge( $this->fields, $selection)));
            //$this->fields = array_keys(array_flip(array_merge( $this->fields, $selection)));
        }else{
            if($alias !== null){
                $selection = $selection.' AS '.$alias;
            }
            $this->fields[]= $selection;
        }

        //$this->fields = func_get_args();
        return $this;
    }

	public function where($condition)
	{
        if(is_null($this->condition)){
            $this->condition = ' WHERE '.$condition;
        }else{
            $this->condition .= ' AND '.$condition;
        }
		return $this;
	}
	
	public function andWhere($condition)
	{
		$this->condition .= ' AND '.$condition;
		return $this;
	}

	public function from($table, $alias = null)
	{
        $alias = $alias? : $this->repository->getTable()[0];
        if (in_array($alias, $this->aliases)) {
            $this->aliases[$alias] = $table;
        }
		if($alias){
			$this->tables[] = $this->getPrefix().$table.'  AS '.$alias;
		}else{
			$this->tables[] = $this->getPrefix().$table.'  AS '.$alias;
		}
		return $this;
	}

    public function setParameter($identifier, $value)
    {
        $this->parameters[':'.$identifier] = $value;

        return $this;
    }

    public function setCriteria($identifier, $value)
    {
        $this->criterias[':'.$identifier] = $value;

        return $this;
    }

    public function orderBy($field, $mode = 'DESC')
    {
        $this->sortBy = ' ORDER BY '.$field.' '.$mode;

        return $this;
    }

    public function leftJoin($join, $alias, $joinTable = null)
    {
        $table = $joinTable? $this->getPrefix().$joinTable:
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
		
	    $this->query =  'SELECT '.implode(', ', array_reverse($this->fields)).' FROM '.
            implode(', ', $this->tables).' '.implode(' ', $this->joins).$this->condition.$this->sortBy;

        return $this;
	}
	
	
	public function getQuery()
	{
		$sql = 'SELECT '
		.  ($this->sqlParts['distinct']===true ? ' DISTINCT' : '')
		 . $this->writeSelect();
		;
		
	}
	
	private function writeSelect()
	{
		return ' '.implode(', ', $this->sqlParts('select');
	}
	
	private function writeFrom()
	{
		$sql = '';
		$fromParts = $this->sqlParts['from');
        $joinParts = $this->sqlParts('join');
		$clauses   = array();
		
		if (!empty($fromParts)) {
            $sql .= ' FROM ';

            foreach ($fromParts as $from) {
                $fromClause = (string) $from;

                if (self::is_assoc($from) && isset($joinParts[$from['alias']])) {
                    foreach ($joinParts[$from['alias']] as $join) {
                        $fromClause .= ' ' . ((string) $join);
                    }
                }

                $fromClauses[] = $fromClause;
            }
        }
	}
	
	private static function is_assoc(array $array)
	{
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}	
	
	private function getSQLQuery()
	
		$dql = 'SELECT'
             . ($this->_dqlParts['distinct']===true ? ' DISTINCT' : '')
             . $this->_getReducedDQLQueryPart('select', array('pre' => ' ', 'separator' => ', '));

        $fromParts   = $this->getDQLPart('from');
        $joinParts   = $this->getDQLPart('join');
        $fromClauses = array();
		
		if ( ! empty($fromParts)) {
            $dql .= ' FROM ';

            foreach ($fromParts as $from) {
                $fromClause = (string) $from;

                if ($from instanceof Expr\From && isset($joinParts[$from->getAlias()])) {
                    foreach ($joinParts[$from->getAlias()] as $join) {
                        $fromClause .= ' ' . ((string) $join);
                    }
                }

                $fromClauses[] = $fromClause;
            }
        }

	}

    public function checkQuery()
    {
        return $this->query;
    }

    public function getResults()
    {
        return $this->repository->query($this->query, $this->parameters, false);
    }

    public function getSingleResult()
    {
        // TODO WTF remove that
        /*$table = $this->tables[0];
        $table = explode(' ', $table);
        $table = ucfirst(array_shift($table));*/

        return $this->repository->query($this->query, array_values($this->parameters), true);
    }

   private function parse_table($join)
   {
       $table = explode('.', $join);

       return array_pop($table);
   }
}