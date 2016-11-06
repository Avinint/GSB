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
    protected $entityPath = 'App\Entity\Class';
    protected $count = 1;
	
	public function getClass()
	{
		if($this->table === null){
			$class = explode("\\", get_called_class());
			$class = str_replace('Table', '', $class);
			$this->table = strtolower(end($class));
		}

		return $this->table;
	}

	public function __construct(Database $db)
	{	
		if(is_null($this->table)){
			$this->db = $db;
			$parts = explode('\\', get_called_class());
			$class = end($parts);
			$this->table = strtolower(str_replace('Table', '', $class));
		}
	}

    public function createQueryBuilder($alias = '', $table='')
    {
        $query = new QueryBuilder($this);
        if($alias === null){
            $alias = $this->getClass()[0]; // Si alias vide on utilise la premiere lettre de la classe
        }
       //$query->addAlias($alias, $this->getClass());
        if($table === ''){
            $table = $this->getClass();
        }
        $query->select($alias.'.*')->from($table, $alias);

        return $query;
    }

    public function getEntity($id, $table)
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

    public function findAll()
    {
        $query = $this->createQueryBuilder('a');
        return $query
            ->getQuery()
            ->getResults();
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
            $table = $this->getPrefix().$this->getClass();
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
       // var_dump($entity->getVars());
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
            $table = $this->getPrefix().$this->getClass();
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
        // change le chemin de table en chemin d'entité
        $entity = $class?  $class: preg_replace('/Table$/i', '', preg_replace('/Table/i', 'Entity', get_called_class(), 1), 1);
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
        $config = Config::getInstance(ROOT.'/Config/Config.php');
        return $config->get('db_prefix');
    }
}