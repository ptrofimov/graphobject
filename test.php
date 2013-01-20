<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Graph\Method;
use Graph\Object;

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
    $method->parent->parent(),
    $method->parent->parent,
]);

$o = new Object();
$o->getNumber = function () {
    return $this->property;
};
$o->getNumber = function () {
    return $this->parent();
};
$o->property='property';
$o->getProperty=function(){
    return $this->property;
};

var_dump([
    $o->getNumber->parent(),
]);
