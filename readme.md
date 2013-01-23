# Graph object

This is the continuation of series of experiments with dynamic objects in PHP.
Crazy, mad and insane series of experiments? Yes, exactly. Thank you.

The first part was about [dynamic objects a-la Javascript](https://github.com/ptrofimov/jslikeobject).
With using $this pointer outside of class methods, dynamic inheritance and other crazy stuff.

Now we are going ahead.

### Dynamic methods

```php
$me = new Method(function () {
    return $this;
});

$this->assertSame($me, $me());
$this->assertNull($me->parent);
```

Using these objects that contains only one method (a-la Closures, but PHP
doesn't permit to change Closure objects) we can inherit methods (behaviour) but not objects (behaviour and state).

```php
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
```

Have created sequence of inherited methods (tree-structure of methods)
we can embed this structure into different contexts, the same time creating objects.

```php
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
```

For other usage see tests/Graph/MethodTest.php

### Dynamic contexts

Let's create usual dynamic object with dynamic propery and dynamic method.

```php
$me = new Object([
    'number' => 1,
    'getNumber' => function () {
        return $this->number;
    },
]);

$this->assertSame(1, $me->getNumber());
```

Have embedded tree-structures of methods into context,
we can inherit them one upon other using simple interface (like stack).

```php
$me = new Object();

$me->setNumber = function ($number) {
    $this->number = $number;

    return $this;
};
$me->setNumber = function ($number) {
    return $this->parent($number);
};

$this->assertNull($me->number);

$this->assertSame($me->setNumber->parent, $me->setNumber(1));
$this->assertSame(1, $me->number);
$this->assertSame($me->setNumber, $me->setNumber->setNumber);
```

This is the way we can inherit methods without changing context (each new method
stays connected to the same set of properties and methods, but doesn't lose
the possibility to have parent and childs).

Have changed context we can imitate usual and natural object inheritance.

This is it.