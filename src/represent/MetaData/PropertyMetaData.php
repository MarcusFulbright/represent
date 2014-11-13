<?php

namespace Represent\MetaData;

/**
 * Value object that can be used to supplement \ReflectionProperty. This allows for more maniuplation
 *
 */
class PropertyMetaData 
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