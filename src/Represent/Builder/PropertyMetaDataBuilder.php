<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Annotations\Property;
use Represent\Enum\PropertyTypeEnum;
use Represent\MetaData\PropertyMetaData;

/**
 * Responsible for building representations of class properties
 *
 * Class PropertyMetaDataBuilder
 */
class PropertyMetaDataBuilder
{
    private $annotaitonReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotaitonReader = $annotationReader;
    }

    /**
     * Responsible for building property representation from \ReflectionProperties in the form of PropertyMetaData
     *
     * @param \ReflectionProperty $property
     * @param $original
     * @return PropertyMetaData
     */
    public function propertyMetaFromReflection(\ReflectionProperty $property, $original)
    {
        $property->setAccessible(true);
        $meta = new PropertyMetaData($property->name, $property->getValue($original), $property->class);

        return $this->parseAnnotations($meta, $property, $original);
    }

    /**
     * Parses properties for annotations and delegates the handling of those annotations
     *
     * @param PropertyMetaData $meta
     * @param \ReflectionProperty $property
     * @param $original
     * @return PropertyMetaData
     */
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

    /**
     * Handles dealing with Represent\Property annotation
     *
     * @param \ReflectionProperty $property
     * @param Property $annot
     * @param PropertyMetaData $meta
     * @param $original
     * @return PropertyMetaData
     */
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

    /**
     * Handles dealing with type conversion for the Represent\Property annotation
     * @param $type
     * @param $value
     * @return bool|\DateTime|int|string
     */
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