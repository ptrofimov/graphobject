<?php
namespace Graph;

class Object
{
    private $_parent;
    private $object = [];

    public function __construct()
    {
        $args = func_get_args();
        if (count($args) == 1 && is_array($args[0])) {
            foreach ($args[0] as $key => $value) {
                $this->{$key} = $value;
            }
        }
        /*elseif(count($args)==1&&$args[0] instanceof Object){

                } else {
                    $object = null;
                    foreach ($args as $arg) {
                        if (is_array($arg)) {
                            $object = new Object($arg);
                        } elseif ($arg instanceof Object) {
                            $object = $arg;
                        } else {
                            throw new \InvalidArgumentException(
                                'Args must be either array or Object instance'
                            );
                        }
                    }
                }*/
    }

    public function __set($key, $value)
    {
        if (!$value instanceof \Closure) {
            $this->object[$key] = $value;
        } else if (!isset($this->object[$key])) {
            $this->object[$key] = new Method($value);
            $this->object[$key]->setContext($this);
        } else {
            $this->object[$key] = new Method($this->object[$key], $value);
            $this->object[$key]->setContext($this);
        }
    }

    public function __get($key)
    {
        return isset($this->object[$key]) ? $this->object[$key] : null;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array(
            $this->{$method},
            $args
        );
    }
}
