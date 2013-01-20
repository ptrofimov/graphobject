<?php

class Object
{
    private $refs = [];
    private $local = [];

    public function __construct()
    {
        $args = func_get_args();
        if (count($args) == 2 && is_object($args[0]) && is_array($args[1])) {
            list($this->refs['parent'], $this->local) = $args;
        } elseif (count($args) == 1 && is_array($args[0])) {
            list($this->local) = $args;
        }
    }

    public function __get($key)
    {
        $value = null;
        if (isset($this->refs[$key])) {
            $value = $this->refs[$key]->{$key};
        } elseif (isset($this->local[$key])) {
            $value = $this->local[$key];
            if ($value instanceof Closure) {
                $value = $value->bindTo($this);
            }
        } elseif (isset($this->refs['parent'])) {
            $value = $this->refs['parent']->{$key};
        }

        return $value;
    }

    public function __set($key, $value)
    {
        if ($key == 'parent') {
            throw new InvalidArgumentException('Forbidden to redefine parent');
        } elseif ($value instanceof Closure
            && isset($this->local[$key])
            && $this->local[$key] instanceof Closure
        ) {
            $this->refs[$key] = new static($this, [$key => $value]);
        } else {
            $this->local[$key] = $value;
        }
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(
            $this->{$method},
            $args
        );
    }

    public function parent()
    {
        return call_user_func_array(
            $this->parent,
        );
    }
}


$o = new Object();
$o->getNumber = function () {
    return 1;
};
$o->getNumber = function () {
    return $this->parent() * 2;
};

var_dump([
    $o->getNumber(),
]);

one_object = one_method
closure
