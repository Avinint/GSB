<?php

namespace Core\Component\Auth;

use Core\Entity\UserInterface;

class Auth
{
    public function login(UserInterface $user, $password)
    {
        return password_verify($password, $user->getMdp()) ? $this->authenticate($user) :  false;
    }

    public function authenticate($user, $id = null)
    {
        $_SESSION = array();
        session_regenerate_id();
        $_SESSION['user'] = serialize($user);

        return  $_SESSION['logged'] = true;
    }

    public function isLogged()
    {
        return isset($_SESSION['logged']) && $_SESSION['logged'];
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . $_SERVER['HTTP_ORIGIN'].$_SERVER['SCRIPT_NAME'].'/');
    }
	
	public function isGranted($role = 'ROLE_USER', $strict = false)
	{
        $user        = $this->getUser();
        $currentRole = $user->getRole()->getNom();
        if ($role === 'ROLE_USER') {
            return $this->isLogged();
        }
        $isGranted   = $strict ? $currentRole === $role : $currentRole === $role || $currentRole === 'ROLE_ADMIN';

        return ($this->isLogged() && $isGranted);
	}

    public function getUser()
    {
        if (isset($_SESSION['user'])) {
            return unserialize($_SESSION['user']);
        }

        return false;
    }
}
