<?php

namespace Represent\Enum\SuperClass;

class AbstractEnum
{
    /**
     * Returns all enum constant values in an array
     *
     * @return array
     */
    public static function toArray()
    {
        $reflectionClass = new \ReflectionClass(get_called_class());
        $parentRelectionClass = $reflectionClass->getParentClass();

        return array_diff(
            $reflectionClass->getConstants(),
            $parentRelectionClass->getConstants()
        );
    }
}