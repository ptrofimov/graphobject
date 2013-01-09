<?php


class Object
{
    private $parent;
    private $properties = [];
    private $methods = [];
    private $object;
    private $heads = [];

    public function __construct()
    {
        $args = func_get_args();
        if (count($args) == 2 && is_object($args[0]) && is_array($args[1])) {
            list($this->parent, $this->object) = $args;
        } elseif (count($args) == 1 && is_array($args[0])) {
            list($this->object) = $args;
        } else {
            throw new \InvalidArgumentException('Invalid parameters');
        }
    }

    public function parent()
    {
        return $this->parent;
    }

    public function __get($key)
    {
        $value = null;
        if (array_key_exists($key, $this->methods)) {
            $value = $this->methods[$key];
        } elseif (array_key_exists($key, $this->object)) {
            $value = $this->object[$key];
        } elseif (is_object($this->parent)) {
            $value = $this->parent->{$key};
        }

        return $value;
    }

    public function __set($key, $value)
    {
        if ($value instanceof Closure
            && isset($this->object[$key])
        ) {
            $this->methods[$key] = new self($this, []);
            $this->methods[$key]->{$key} = $value;
            //$this->heads[$key] = $this->methods[$key];
        } elseif ($value instanceof Closure) {
            $this->object[$key] = $value;
        } else {
            $this->object[$key] = $value;
        }
    }

    public function __call($method, array $args)
    {
        $value = null;
        if (array_key_exists($method, $this->methods)) {
            $value = call_user_func_array(
                $this->methods[$method]->{$method}->bindTo($this->methods[$method]),
                $args
            );
        }elseif(array_key_exists($method, $this->object)){
            $value = call_user_func_array(
                $this->object[$method]->bindTo($this),
                $args
            );
        } elseif (is_object($this->parent)) {
            $value = call_user_func_array([$this->parent, $method], $args);
        }

        return $value;
    }
}


$o = new Object([]);
$o->getNumber = function () {
    return 1;
};
$o->getNumber = function () {
    return 2;
};

var_dump([
    $o->getNumber(),
]);
