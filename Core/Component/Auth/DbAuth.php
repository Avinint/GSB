<?php

namespace Core\Component\Auth;

use Core\Entity\UserInterface;

class DbAuth
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function login($user, $password)
    {
        return $user instanceof UserInterface && password_verify($password, $user->getMdp()) ? $this->authenticate($user) :  false;
    }

    public function authenticate($user, $id = null)
    {
        $_SESSION['auth'] = $id ? : $user->getId();
        $_SESSION['role'] = false == $user->getRole() ? 'ROLE_USER' :$user->getRole()->getNom();

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
        return $this->logged() && $this->roleHierarchyVoter($role);
    }

	public function getUserId()
    {
        if($this->logged()){
            return $_SESSION['auth'];
        }

        return false;
    }

    public function  roleHierarchy()
    {
        return $hierarchy = $this->config->get('role_hierarchy');
    }

    public function roleHierarchyVoter($role)
    {
        if($role === $_SESSION['role']) {
            return true;
        }
        $roles[] = $_SESSION['role'];

        foreach ($this->roleHierarchy() as $tree => $children)
        {
            if ($_SESSION['role'] === $tree) {
                foreach ($children as $child) {

                    if ($role === $child) {
                        return true;
                    }
                    if (!in_array($child, $roles)) {
                        $roles[] = $child;
                    }
                    if (array_intersect($roles, $this->roleHierarchy())) {
                        return true;
                    }
                }
            }
        }

        return false;
    }


}
