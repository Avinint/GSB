<?php

namespace App\AppModule\Entity;

use \App;
use Core\Entity\Entity;
use Core\Entity\UserInterface;

class Utilisateur extends Entity implements UserInterface{

    protected $id;
    protected $login;
    protected $nom;
    protected $prenom;
    protected $email;
    protected $mdp;
    protected $role;
    protected $pays;

    public function __construct($mdp = null)
    {
        if($mdp) {
            $this->mdp = $mdp;
        }

       /* $values = func_get_args();
        var_dump($values);
        $this->initialize($values);*/
    }

    private function initialize($values)
    {
        $class = new \ReflectionClass($this);
        $construct = $class->getConstructor();
        $params = $construct->getParameters();
        foreach($params as $key => $arg) {
            $prop = $arg->name;
            $value = $values[$key];
            if(!is_null($value)) {
                $this->$prop = $value;
            }
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }


    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $mdp
     */
    public function setMdp($mdp)
    {
        $mdp = password_hash ($mdp, PASSWORD_BCRYPT);
        $this->mdp = $mdp;
    }

    /**
     * @return mixed
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param Role $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $pays_id
     */
    public function setPays($pays)
    {
        $this->pays = $pays;
    }

    /**
     * @return mixed
     */
    public function getPays()
    {
        return $this->pays;
    }

    public function getFilePath()
    {
        return  ROOT.D_S.'public'.D_S.'img'.D_S.'avatars'.D_S;
    }
}