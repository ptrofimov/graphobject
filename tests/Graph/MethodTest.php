<?php
namespace Graph;

class MethodTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $me = new Method(function () {
            return $this;
        });

        $this->assertSame($me, $me());
        $this->assertNull($me->parent);
    }

    public function testInheritance()
    {
        $first = new Method(function () {
            return 1;
        });
        $second = new Method($first, function () {
            return $this->parent() * 2;
        });

        $this->assertSame(2, $second());
        $this->assertSame($first, $second->parent);
        $this->assertSame(1, $second->parent());
        $this->assertNull($second->parent->parent);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgs()
    {
        new Method();
    }

    public function testSetContext()
    {
        $object = new \StdClass();
        $object->property = 'property';
        $me = new Method(function () {
            return $this->property;
        });

        $this->assertNull($me());

        $me->setContext($object);

        $this->assertSame('property', $me());
    }
}