<?php
namespace Graph;

class Method
{
    private $_parent;
    private $method;
    private $context;
    private $properties = [];

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
        $this->context = $this;
    }

    public function setContext($context)
    {
        if (!is_object($context)) {
            throw new \InvalidArgumentException('Context must be object');
        }
        $this->context = $context;

        return $this;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function __get($key)
    {
        if ($key == 'parent') {
            return $this->_parent;
        }

        return $this->context !== $this
            ? $this->context->{$key}
            : (isset($this->properties[$key]) ? $this->properties[$key] : null);
    }

    public function __set($key, $value)
    {
        if ($this->context !== $this) {
            $this->context->{$key} = $value;
        } else {
            $this->properties[$key] = $value;
        }
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

    public function extend(\Closure $method)
    {
        return new Method($this, $method);
    }
}
