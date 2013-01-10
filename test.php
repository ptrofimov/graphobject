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
            $value = $this->refs[$key];
        } elseif (array_key_exists($key, $this->local)) {
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
            throw new BadMethodCallException('Forbidden to redefine parent');
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
        $value = null;
        $pointer = isset($args['__pointer__']) ? $args['__pointer__'] : $this;
        if (isset($this->refs[$method]) && $this->refs[$method] !== $pointer) {
            $args['__pointer__'] = $pointer;
            $value = call_user_func_array(
                $this->refs[$method]->{$method},
                $args
            );
        } elseif (
            isset($this->local[$method])
            && $this->local[$method] instanceof Closure
        ) {
            unset($args['__pointer__']);
            $value = call_user_func_array(
                $this->local[$method],
                $args
            );
        }

        return $value;
    }
}


$o = new Object();
$o->getNumber = function () {
    return 1;
};
$o->getNumber = function () {
    return $this->parent;
};

var_dump([
    $o->getNumber(),
]);
