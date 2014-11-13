<?php

namespace Represent\MetaData;

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