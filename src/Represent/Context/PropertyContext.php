<?php

namespace Represent\Context;

/**
 * Value object that can be used to supplement \ReflectionProperty. This allows for more manipulation
 */
class PropertyContext
{
    public $name;

    public $value;

    public $class;

    public function __construct($name, $value, $class)
    {
        $this->name = $name;
        $this->class = $class;
        $this->value = $value;
    }
}