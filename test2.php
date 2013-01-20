<?php

class Method
{
    private $_parent;
    private $method;

    public function __construct()
    {
        $args = func_get_args();
        if (count($args) == 2
            && $args[0] instanceof Method
            && $args[1] instanceof Closure
        ) {
            list($this->_parent, $this->method) = $args;
        } elseif (count($args) == 1
            && $args[0] instanceof Closure
        ) {
            list($this->method) = $args;
        } else {
            throw new InvalidArgumentException('Invalid args');
        }
        $this->method = $this->method->bindTo($this);
    }

    public function __get($key)
    {
        return $key == 'parent' ? $this->_parent : null;
    }

    public function parent()
    {
        return $this->parent
            ?
            call_user_func_array(
                $this->parent,
                func_get_args()
            ) : null;
    }

    public function __invoke()
    {
        return call_user_func_array(
            $this->method,
            func_get_args()
        );
    }
}

$method = new Method(function () {
    return 1;
});

$method = new Method($method, function () {
    return 2;
});

$method = new Method($method, function () {
    return 3;
});

var_dump([
    $method(),
    $method->parent(),
    $method->parent()->parent(),
    $method->parent->parent,
]);