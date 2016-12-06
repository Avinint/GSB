<?php

namespace Core\Component\Auth;

use Core\Entity\UserInterface;

class DbAuth
{
	/**
	* @param $username
	* @param @password
	* @return boolean
	*/
    public function login(UserInterface $user, $password)
    {
        return password_verify($password, $user->getMdp()) ? $this->authenticate($user) :  false;
    }

    public function authenticate($user, $id = null)
    {
        $_SESSION['auth'] = $id ? : $user->getId();
        $_SESSION['role'] = $user->getRole()->getNom();
        return  $_SESSION['logged'] = true;
    }

    public function isLogged()
    {
        return isset($_SESSION['logged']);
    }

	public function logged()
	{
		return $_SESSION['logged'];
	}
	
	public function isGranted($role = 'ROLE_USER')
	{
        var_dump($_SESSION['role']);
		return ($this->logged() && $_SESSION['role'] === $role);
	}
	
	public function getUserId()
	{
		if($this->logged()){
			return $_SESSION['auth'];
		}
		return false;
	}
}
