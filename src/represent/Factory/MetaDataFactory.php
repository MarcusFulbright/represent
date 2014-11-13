<?php

namespace Represent\Factory;

use Represent\MetaData\PropertyMetaData;

class MetaDataFactory 
{
    public function propertyMetaFromReflection(\ReflectionProperty $property, $original)
    {
        $property->setAccessible(true);
        return new PropertyMetaData($property->getName(), $property->getValue($original), $property->class);
    }
}
