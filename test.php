<?php
require_once(__DIR__ . '/vendor/autoload.php');

$o = new Graph\Object();
$o->getNumber = function () {
    return $this->property;
};
$o->getNumber = function () {
    return $this->parent();
};
$o->property = 'property';
$o->getProperty = function () {
    return $this->property;
};

var_dump([
    $o->getNumber->parent(),
]);
