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

        if( password_verify($password, $user->getMdp())){
            $_SESSION['auth'] = $user->getId();
            $_SESSION['role'] = $user->getRole_id();
            return true;
        }
        return false;
    }

	public function logged()
	{
		 return isset($_SESSION['auth']);
	}
	
	public function isGranted($role = 1)
	{
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
