<?php

namespace Core\Table;

use Core\Component\Database\QueryBuilder;
use \App;
use Core\Config;
use Core\Entity\Entity;

class Table {
	
	protected $table;
	protected $db;
    protected $entity;
    protected $count = 1;

    public function getTable()
    {
        $this->getDbTableName();

        return $this->table;
    }

    public function getDbTableName()
    {
        if (empty($this->table)) {
            $app = App::getInstance();
            $class = explode("\\", get_called_class());
            $class = end($class);
            $this->table = $app->decamelize(str_replace('Table', '', $class));
        }
    }

    public function getEntityClass()
    {
        $class = explode(':', $this->entity);
        $module = array_shift($class);
        $class = array_shift($class);
        $class = 'App\\'.$module.'\\Entity\\'.$class;

        return $class;
    }

    private function getEntity()
    {
        $entity = $this->entity ?
        $this->getEntityClass() :
        preg_replace('/Table$/i', '', preg_replace('/Table/i', 'Entity', get_called_class(), 1), 1);

        return $entity;
    }

    public function __construct($entity = null)
    {
        $app = App::getInstance();
        $this->db = $app->getContainer('db');

        if ($entity) {
            $this->entity = $entity;
            $entity = explode(':', $entity);
            $entity = end($entity);
            $this->table = $app->decamelize($entity);
        } else {
            $this->getDbTableName();
        }
    }

    public function createQueryBuilder($alias = '', $table = '')
    {
        $query = new QueryBuilder($this);
        if($alias === null){
            $alias = strtolower($this->getTable()[0]); // Si alias vide on utilise la premiere lettre de la classe
        }

        //$query->addAlias($alias, $this->getTable());
        if($table === ''){
            $table = $this->getTable();
        }
        $query->select($alias)->from($table, $alias);

        return $query;
    }

    /* TODO remove used for debug */
    public function findEntity($id, $table)
    {
        $query = $this->createQueryBuilder($table[0], $table)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getSingleResult();
    }

    public function find($id)
    {
        $query = $this
        ->createQueryBuilder('a')
        ->limit(0, 1)
        ->getQuery()
        ;

        return $query->getSingleResult();
    }

    public function findOneBy(array $criteria)
    {
        $query = $this->loadCriteria($criteria, $orderBy = array(), $limit = null, $offset = null);

        return $query->getQuery()->getSingleResult();
    }

    public function findBy(array $criteria, array $orderBy = array(), $limit = null, $offset = null)
    {
        // TODO support for limits
        $query = $this->loadCriteria($criteria, $orderBy = array(), $limit = null, $offset = null);

            if ($orderBy) {
                $query->orderBy($orderBy[0], $orderBy[1])
            ;
        }

        return $query->getQuery()->getResults();
    }

    private function loadCriteria(array $criteria, array $orderBy = array(), $limit = null, $offset = null)
    {
        $query = $this ->createQueryBuilder('a');

        return $query->where(key($criteria).' = :'.key($criteria))
            ->setParameter(key($criteria), $criteria[key($criteria)]);
    }

    public function findAll(array $orderBy = null)
    {
        $query = $this->createQueryBuilder('a');
        if ($orderBy) {
            $query->orderBy(':sort', ':order')
            ->setParameter('sort', $orderBy[0])
            ->setParameter('sort', $orderBy[1])
            ->getQuery();
        }

        return $query
            ->getQuery()
            ->getResults();
    }

   /**  Magic finder */
    public function __call($method, $arguments)
    {
        switch (true) {
            case (0 === strpos($method, 'findBy')):
                $by = substr($method, 6);
                $method = 'findBy';
                break;

            case (0 === strpos($method, 'findOneBy')):
                $by = substr($method, 9);
                $method = 'findOneBy';
                break;

            default:
                throw new \Exception(
                    "Undefined method '$method'. The method name must start with ".
                    "either findBy or findOneBy!"
                );
        }

        if (empty($arguments)) {
                $arguments = array();
        }
;        $fieldName = lcfirst($by);

        //if ($this->_class->hasField($fieldName) || $this->_class->hasAssociation($fieldName)) {
        switch (count($arguments)) {
            case 1:
                return $this->$method(array($fieldName => $arguments[0]));

            case 2:
                return $this->$method(array($fieldName => $arguments[0]), $arguments[1]);

            case 3:
                return $this->$method(array($fieldName => $arguments[0]), $arguments[1], $arguments[2]);

            case 4:
                return $this->$method(array($fieldName => $arguments[0]), $arguments[1], $arguments[2], $arguments[3]);

            default:
                // Do nothing
        }

        throw new \Exception($this->entity, $fieldName, $method.$by);
    }


    public function extract($key, $value)
    {
        $repo = $this->findAll();
        $return = array();
        foreach ($repo as $v){
            $return[$v->$key] = $v->$value;
        }
        return $return;
    }

    public function pluck($args)
    {
        $args = func_get_args($args);
        $id = is_int($args[0]) ? array_shift($args) : 0 ;
        $args = array_map(function($arg) {
                return 't'.$arg;
            }, $args);

        $query = $this->createQueryBuilder('t')->select($args);
        if ($id) {
            return $query
                ->where('id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleResult();
        }
        return $query
            ->getQuery()
            ->getResults();
    }

    public function refresh($entity, $fields)
    {
        if(!$entity instanceof Entity){
            throw new \Exception("Database problem");
        }
        $vars = $entity->getVars();
        foreach($fields as $field => $value){
            if(array_key_exists($field, $vars)){
                if($vars[$field]!== $value){
                    $entity->$field = $value;
                }
            }
        }
        return $entity;
    }

    public function update($entity, $image = null, $table = '')
    {
        if($table === ''){
            $table = $this->getPrefix().$this->getTable();
        }else{
            $table = $this->getPrefix().$table;
        }

        $fields = array_filter($entity->getVars());
        /* * * * * A mettre dans handleRequest ? */  // on n'update que ce que le champs mis à jours
        if ($entity->getId()) {
            $preUpdateState = $fields = $entity->getId() ? $this->find($entity->getId())->getVars() : array();
            $fields = array_diff_assoc($fields, $preUpdateState);
			$imageBackup = method_exists($entity, 'getImage') ? $entity->getImage(): '';
        }

        if(empty($image) || $image['image']['name'] === ''){
            unset($image);
        }
        
        //$filePath = $table === 'article'?'':D_S.$table.'s';
        //$path = ROOT.D_S.'public'.D_S.'img'.$filePath;
        if (isset($image)) {
            $fields['image'] = $entity->preUpload($image['image']);
        }

        //$entity = $this->refresh($entity, $fields);

		$sql_parts = [];
		$attributes = [];

		foreach($fields as $k => $v){
			$sql_parts[] = "$k = ?";
			$attributes[] = "$v";
		}
		$attributes[] = $entity->id;

		$sql = implode(', ', $sql_parts);

        if ($this->query (
        'UPDATE '.$table.'
        SET '.$sql.'
        WHERE id = ?
        ', $attributes,true)) {
            if (isset($image)) {
               if ($uploaded = $entity->upload($image['image'], $fields['image'])) {
                   $entity->getId()? $entity->removeFile($imageBackup): NULL;
               } else {
                    $entity->getId()? $entity->setImage($imageBackup): NULL;
                   echo "Fichier non telecharge";
               }
            }

            return true;
        }

        return false;
	}

	public function create(&$entity, $image = null, $table = '')
	{
        if($table === ''){
            $table = $this->getPrefix().$this->getTable();
        }else{
            $table = $this->getPrefix().$table;
        }
		$fields = array_filter($entity->getVars());
        if(empty($image) || $image['image']['name'] === ''){
            unset($image);
        }
        //$filePath = $table === 'article'?'':D_S.$table.'s';
        //$path = ROOT.D_S.'public'.D_S.'img'.$filePath;
        //var_dump($image);
        if (isset($image)) {
            $fields['image'] = $entity->preUpload($image['image']);
        }
		$sql_parts = [];
		$attributes = [];
		
		foreach($fields as $k => $v){
			$sql_parts[] = "$k = ?";
			$attributes[] = "$v";
		}

		$sql = implode(', ', $sql_parts);

        if ($this->query(
            'INSERT INTO '.$table.'
            SET '.$sql,
            $attributes,true)) {
            if (isset($image)) {
                if ($uploaded = $entity->upload($image['image'], $fields['image'])) {

                }else{
                    echo "Fichier non telecharge";
                }
            }

			if (!$entity->getId()) {
				 $entity->setPersistedId($this->lastInsertId()); // on set l'id
				 //$entity = $this->find($this->lastInsertId()); // ou  on récupere l'entite avec l'ID
			}

            return true;
        }

        return false;
    }

	public function delete($id)
	{
        $entity = $this->find($id);
        if(method_exists($entity,'getImage')){
            $image = $entity->getImage();
            $entity->removeFile($image);
        }

		return $this->query('DELETE FROM '.$this->table.' WHERE id = ?',
		array($id), true);
	}

	public function query($statement, $attributes = null, $one = false, $class = null)
	{
        $entity = $this->getEntity();
		$app = App::getInstance();
		if($attributes){
			return 	$app->getDb()->prepare(
						$statement, 
						$attributes,
                        $entity,
						$one
					);
		}else{
			return 	$app->getDb()->query(
						$statement,
                        $entity,
						$one
					);
		}
	}

    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    public function getPrefix()
    {
        $config = Config::getInstance(ROOT.'/config/dbConfig.php', ROOT.'/config/config.php', ROOT.'/config/security.php');
        return $config->get('db_prefix');
    }
}