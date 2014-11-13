<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Annotations\Property;
use Represent\Enum\PropertyTypeEnum;
use Represent\MetaData\PropertyMetaData;

class PropertyMetaDataBuilder
{
    private $annotaitonReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotaitonReader = $annotationReader;
    }

    public function propertyMetaFromReflection(\ReflectionProperty $property, $original)
    {
        $property->setAccessible(true);
        $meta = new PropertyMetaData($property->name, $property->getValue($original), $property->class);
        return $this->parseAnnotations($meta, $property, $original);
    }

    private function parseAnnotations(PropertyMetaData $meta, \ReflectionProperty $property, $original)
    {
        foreach ($this->annotaitonReader->getPropertyAnnotations($property) as $annot) {
            switch (true):
                case ($annot instanceof \Represent\Annotations\Property):
                   $meta = $this->handleRepresentProperty($property, $annot, $meta, $original);
            endswitch;
        }

        return $meta;
    }

    private function handleRepresentProperty(\ReflectionProperty $property, Property $annot, PropertyMetaData $meta, $original)
    {
        if ($annot->getName()) {
            $meta->name = $annot->getName();
        }

        if ($annot->getType()) {
            $meta->value = $this->handleTypeConversion($annot->getType(), $property->getValue($original));
        }

        return $meta;
    }

    private function handleTypeConversion($type, $value)
    {
        switch ($type):
            case PropertyTypeEnum::STRING:
                $value = (string) $value;
                break;
            case PropertyTypeEnum::BOOLEAN:
                $value = (boolean) $value;
                break;
            case PropertyTypeEnum::INTEGER:
                $value = (integer) $value;
                break;
            case PropertyTypeEnum::DATETIME;
                $value = new \DateTime($value);
                break;
        endswitch;

        return $value;
    }
}