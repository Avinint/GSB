<?php

namespace Core\Component\Database;

use \PDO;

class MySQLDatabase {

    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_host;
    private $pdo;

    public function __construct($db_name, $db_user = 'root', $db_pass = '', $db_host = 'localhost')
    {
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_host = $db_host;
    }

    public function getPDO()
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name, $this->db_user, $this->db_pass);
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $this->pdo->exec('SET NAMES utf8');
        }

        return $this->pdo;
    }

    public function query($statement, $class = null, $one = false)
    {
        $data = null;
        $req = $this->getPDO()->query($statement);
        if(is_null($class)){
            $req->setFetchMode(PDO::FETCH_OBJ);
        }else{
            $req->setFetchMode(PDO::FETCH_ASSOC);
        }
        if($one === true){
            $data = $req->fetch();
        }
        else if($one === false){
            $data = $req->fetchAll();
        }
		
        return $data;
    }

    public function prepare($statement, $attr, $class = null, $one = false, $ctor = null)
    {
        $data = null;
        $req = $this->getPDO()->prepare($statement);
        $res = $req->execute($attr);
        if (
            strpos($statement, 'UPDATE') === 0 ||
            strpos($statement, 'INSERT') === 0 ||
            strpos($statement, 'DELETE') === 0
        ) {
            return $res;
        }

        if (is_null($class)) {
            $req->setFetchMode(PDO::FETCH_ASSOC);
        } else {
            if (!is_null($ctor)) {
                $req->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $class, $ctor);
            } else {
                var_dump($statement);
                //$req->setFetchMode(PDO::FETCH_CLASS, $class);
                $req->setFetchMode(PDO::FETCH_ASSOC);
            }
        }

        if($one){
            $data = $req->fetch();
        }
        else if($one === false){
           $data = $req->fetchAll();
        }

        return $data;
    }

    public function lastInsertId()
    {
        return $this->getPDO()->lastInsertId();
    }
}