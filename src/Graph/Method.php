<?php
namespace Graph;

class Method
{
    private $_parent;
    private $method;
    private $context;

    public function __construct()
    {
        $args = func_get_args();
        if (count($args) == 2
            && $args[0] instanceof Method
            && $args[1] instanceof \Closure
        ) {
            list($this->_parent, $this->method) = $args;
        } elseif (count($args) == 1
            && $args[0] instanceof \Closure
        ) {
            list($this->method) = $args;
        } else {
            throw new \InvalidArgumentException('Invalid args');
        }
        $this->method = $this->method->bindTo($this);
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function __get($key)
    {
        return $key == 'parent'
            ?
            $this->_parent : ($this->context ? $this->context->{$key} : null);
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
