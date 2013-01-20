<?php
namespace Graph;

class Object
{
    private $methods = [];

    public function __set($key, $value)
    {
        if (!isset($this->methods[$key])) {
            $this->methods[$key] = new Method($value);
        } else {
            $this->methods[$key] = new Method($this->methods[$key], $value);
        }
    }

    public function __get($key)
    {
        return isset($this->methods[$key]) ? $this->methods[$key] : null;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(
            $this->{$method},
            $args
        );
    }
}
