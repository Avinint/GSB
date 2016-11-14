<?php

namespace Core\Table;

use Core\Database\Database;
use Core\Database\QueryBuilder;
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
        if(is_null($this->table)){
            $app = App::getInstance();
            $class = explode("\\", get_called_class());
            $class = end($class);
            $this->table = $app->decamelize(str_replace('Table', '', $class));
        }
    }

    public function getEntityClass()
    {
        $module = ''; $class = '';
        if($this->entity){
            $class = explode(':', $this->entity);
            $module = array_shift($class);
            $class = array_shift($class);
           $class = 'App\\'.$module.'\\Entity\\'.$class;
        }
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
        $this->db = $app->getDb();
        if($entity){
            $this->entity = $entity;
            $this->table = $app->decamelize($entity);
        }else{
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
        $query->select($alias.'.*')->from($table, $alias);

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
            ->where('id = :id')
            ->setParameter('id', $id)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    public function findOneBy(array $criteria)
    {
        $query = $this
            ->createQueryBuilder('a')
            ->where(':criteria = :value')
            ->setParameter('criteria', key($criteria))
            ->setParameter('value', $criteria[key($criteria)])
            ;

        var_dump($query);
        $query->getQuery();
        return $query->getSingleResult();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        // TODO support for limits
        $query = $this
            ->createQueryBuilder('a')
            ->select('a.'.key($criteria));
        if ($criteria) {
            $query->where(':criteria = :value')
                ->setParameter('criteria', key($criteria))
                ->setParameter('value', $criteria[key($criteria)]);
        }
        if ($orderBy) {
            $query->orderBy(':sort', ':order')
            ->setParameter('sort', $orderBy[0])
            ->setParameter('sort', $orderBy[1])
            ->getQuery();
        }

        return $query->getResults();
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

    /** Allows magic finders
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws
     * @throws \BadMethodCallException
     * @throws
     */
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
        var_dump($arguments);var_dump($method);

        if (empty($arguments)) {
            throw new  \Exception($method.' requires parameters: '.$by);
        }


;        $fieldName = lcfirst($by);var_dump($fieldName);var_dump(count($arguments));

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
        //}

        throw new Exception($this->_entityName, $fieldName, $method.$by);
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

	public function update($entity, $fields, $image = null, $table = '')
	{
        if($table === ''){
            $table = $this->getPrefix().$this->getTable();
        }else{
            $table = $this->getPrefix().$table;
        }

        if(empty($image) || $image['image']['name'] === ''){
            unset($image);
        }
        $imageBackup = method_exists($entity, 'getImage') ?$entity->getImage(): '';

        if(!$entity instanceof Entity){
            throw new \Exception("Database problem");
        }


        //$filePath = $table === 'article'?'':D_S.$table.'s';
        //$path = ROOT.D_S.'public'.D_S.'img'.$filePath;
        if(isset($image)){

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


        if($this->query(
		'UPDATE '.$table.'
		 SET '.$sql.'
		 WHERE id = ?
		', $attributes,true)){
            if(isset($image)){

               if( $uploaded = $entity->upload($image['image'], $fields['image'])){
                   $entity->removeFile($imageBackup);
               }else{
                   $entity->setImage($imageBackup);
                   echo "Fichier non telecharge";
               }
            }
            return true;
        }
        return false;
	}
	
	public function create($entity, $fields, $image = null, $table = '')
	{
        if($table === ''){
            $table = $this->getPrefix().$this->getTable();
        }else{
            $table = $this->getPrefix().$table;
        }

        if(empty($image) || $image['image']['name'] === ''){
            unset($image);
        }
        //$filePath = $table === 'article'?'':D_S.$table.'s';
        //$path = ROOT.D_S.'public'.D_S.'img'.$filePath;
        //var_dump($image);

        if(isset($image)){
            $fields['image'] = $entity->preUpload($image['image']);
        }

		$sql_parts = [];
		$attributes = [];

		foreach($fields as $k => $v){
			$sql_parts[] = "$k = ?";
			$attributes[] = "$v";
		}
		$sql = implode(', ', $sql_parts);

        if($this->query(
            'INSERT INTO '.$table.'
            SET '.$sql,
            $attributes,
            true)){
            if(isset($image)){
                if( $uploaded = $entity->upload($image['image'], $fields['image'])){

                }else{
                    echo "Fichier non telecharge";
                }
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


		return $this->query(
		'DELETE  FROM '.$this->table.'
		 WHERE id = ?
		',
		array($id),
		true);
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
        $config = Config::getInstance(ROOT.'/Config/dbConfig.php', ROOT.'/Config/config.php', ROOT.'/Config/security.php');
        return $config->get('db_prefix');
    }
}