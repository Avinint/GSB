<?php

namespace Core\Routing;

class Route
{
    private $name;
    private $path;
    private $parameters;

    function __construct($name, $path, $parameters = null)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->path = $path;
    }


    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }


    public function __toString()
    {
        return "/";
    }
}