<?php

namespace Core\Database;

use Core\Table\Table;

class QueryBuilder{
	
	private $fields = array();
    private $aliases = array();
	private $tables = array();
    private $parentTable;
	private $condition;
    private $repository;
    private $query;
    private $sortBy;
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
        var_dump($this->getPrefix());
        return $this->repository;
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
            $this->fields =  $selection;
       }else{
            $this->fields = array();
            $this->fields[]= $selection;
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
            $this->fields[] = array_shift($aliases).'.*'; //
            return $this;

        }else if(is_array($selection)){
            $this->fields = array_flip(array_flip(array_merge( $this->fields, $selection)));
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

		if($alias){
			$this->tables[] = $this->getPrefix().$table.'  AS '.$alias;
		}else{
			$this->tables[] = $this->getPrefix().$table;
		}
		return $this;
	}

    public function setParameter($identifier, $value)
    {
        $this->parameters[':'.$identifier] = $value;

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
            implode(', ', $this->tables).implode(' ', $this->joins).$this->condition.$this->sortBy;

        return $this;
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
        $table = $this->tables[0];
        $table = explode(' ', $table);
        $table = ucfirst(array_shift($table));
        return $this->repository->query($this->query, $this->parameters, true);
    }

   private function parse_table($join)
   {
       $table = explode('.', $join);

       return array_pop($table);
   }
}