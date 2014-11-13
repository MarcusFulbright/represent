<?php

namespace Represent\MetaData;

class PropertyMetaData 
{
    public $name;

    public $class;

    public $value;

    public function __construct($name, $value, $class)
    {
        $this->name  = $name;
        $this->value = $value;
        $this->class = $class;
    }
}