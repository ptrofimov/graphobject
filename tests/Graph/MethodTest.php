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
        $first = new Method(function ($number) {
            return $number;
        });
        $second = new Method($first, function () {
            return $this->parent(1) * 2;
        });

        $this->assertSame(2, $second());
        $this->assertSame($first, $second->parent);
        $this->assertSame(1, $second->parent(1));
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
        $context = new \StdClass();
        $me = new Method(function () {
            return 1;
        });

        $this->assertSame($me, $me->getContext());

        $this->assertSame($me, $me->setContext($context));
        $this->assertSame($context, $me->getContext());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidContext()
    {
        $me = new Method(function () {
            return 1;
        });
        $me->setContext(null);
    }

    public function testExpandContext()
    {
        $context = new \StdClass();
        $context->property = 'context';

        $me = new Method(function () {
            return $this->property;
        });

        $this->assertNull($me());
        $this->assertNull($me->property);
        $this->assertSame('context', $context->property);

        $me->property = 'method';

        $this->assertSame('method', $me());
        $this->assertSame('method', $me->property);
        $this->assertSame('context', $context->property);

        $me->setContext($context);

        $this->assertSame('context', $me());
        $this->assertSame('context', $me->property);
        $this->assertSame('context', $context->property);

        $me->property = 'updated';

        $this->assertSame('updated', $me());
        $this->assertSame('updated', $me->property);
        $this->assertSame('updated', $context->property);
    }

    public function testExtend()
    {
        $m1 = new Method(function () {
            return 1;
        });
        $m2 = $m1->extend(function () {
            return 2;
        });

        $this->assertSame(1, $m1());
        $this->assertSame(2, $m2());
        $this->assertSame(1, $m2->parent());
    }
}