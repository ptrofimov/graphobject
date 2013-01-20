<?php
namespace Graph;

class ObjectText extends \PHPUnit_Framework_TestCase
{
    public function testSetGetProperty()
    {
        $me = new Object();

        $this->assertNull($me->property);

        $me->property = 'property';

        $this->assertSame('property', $me->property);
    }

    public function testMethods()
    {
        $me = new Object();

        $me->setNumber = function ($number) {
            $this->number = $number;

            return $this;
        };
        $me->getNumber = function () {
            return $this->number;
        };

        $this->assertNull($me->number);

        $this->assertSame($me->setNumber, $me->setNumber(1));
        $this->assertSame(1, $me->getNumber());
        $this->assertSame(1, $me->number);

        $this->assertSame($me->setNumber, $me->setNumber(2));
        $this->assertSame(2, $me->getNumber());
        $this->assertSame(2, $me->number);
    }
}