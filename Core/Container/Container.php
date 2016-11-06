<?php

namespace Core\Container;


class Container implements  \ArrayAccess
{

    private $registry = array();
    private $shared = array();

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->registry[] = $value;
        } else {
            $this->registry[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->registry[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->registry[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->registry[$offset]) ? $this->registry[$offset] : null;
    }

    public function set($key, Callable $resolver)
    {
        if(!isset($this->shared[$key])){
            $this->shared[$key] = $resolver;
        }
        $this[$key] = $resolver();

        return $this;
    }

    public function register($key, Callable $resolver){
        $this->registry[$key] = $resolver;
    }

    public function addArgument($arg)
    {

    }

    public function setInstance($object)
    {
        $reflection = new \ReflectionClass($object);
    }

    public function get($key)
    {
        if(!isset($this->shared[$key])){
            $this->shared[$key] = $this->registry[$key];
        }

        return $this->shared[$key];
    }
}