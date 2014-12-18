<?php

namespace Represent\Handler;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Enum\PropertyTypeEnum;
use Represent\Annotations\Property;

/**
 * Class PropertyHandler
 *
 * Simple Class that handles the property annotation and converting property values to the type given in the property annotation
 */
class PropertyHandler
{
    /**
     * @var Doctrine\Common\Annotations\AnnotationReader
     */
    private $reader;

    /**
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Returns the property annotation or false if one is not present;
     *
     * @param \ReflectionProperty $property
     * @return bool|Property
     */
    public function getPropertyAnnotation(\ReflectionProperty $property)
    {
        $annot = $this->reader->getPropertyAnnotation($property, '\Represent\Annotations\Property');

        if (!$annot) {
            return false;
        }

        return $annot;
    }

    /**
     * Returns the serialized name for this property
     *
     * @param \ReflectionProperty $property
     * @param Property           $annot
     * @return string
     */
    public function getSerializedName(\ReflectionProperty $property, Property $annot = null)
    {
        if ($annot || $annot = $this->getPropertyAnnotation($property)) {

            return $annot->getName();
        }

        return $property->getName();
    }

    /**
     * Returns the type this property is changed to during serialization or false if no conversion occurs
     *
     * @param \ReflectionProperty $property
     * @param Property           $annot
     * @return null|string
     */
    public function propertyTypeOverride(Property $annot = null, \ReflectionProperty $property = null)
    {
        if ($annot || $annot = $this->getPropertyAnnotation($property)) {

            return $annot->getType();
        }

        return null;
    }

    /**
     * Returns the value used for serialization from a reflection property
     *
     * @param \ReflectionProperty $property
     * @param                    $original
     * @param Property           $annot
     * @return bool|DateTime|int|string
     */
    public function getConvertedValue(\ReflectionProperty $property, $original, Property $annot = null)
    {
        return $this->handleTypeConversion($this->propertyTypeOverride($annot, $property), $property->getValue($original));
    }

    /**
     * Converts $value to $type
     *
     * @param $type
     * @param $value
     * @return bool|\DateTime|int|string
     */
    public function handleTypeConversion($type, $value)
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