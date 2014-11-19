<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Annotations\Property;
use Represent\Enum\PropertyTypeEnum;
use Represent\Context\PropertyContext;

/**
 * Responsible for building representations of class properties
 */
class PropertyContextBuilder
{
    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Responsible for building property representation from \ReflectionProperties in the form of PropertyContext
     *
     * @param \ReflectionProperty $property
     * @param $original
     * @return PropertyContext
     */
    public function propertyContextFromReflection(\ReflectionProperty $property, $original)
    {
        $property->setAccessible(true);
        $context = new PropertyContext($property->name, $property->getValue($original), $property->class);

        return $this->parseAnnotations($context, $property, $original);
    }

    /**
     * Parses properties for annotations and delegates the handling of those annotations
     *
     * @param PropertyContext     $context
     * @param \ReflectionProperty $property
     * @param $original
     * @return PropertyContext
     */
    private function parseAnnotations(PropertyContext $context, \ReflectionProperty $property, $original)
    {
        $annot = $this->annotationReader->getPropertyAnnotation($property, '\Represent\Annotations\Property');

        if ($annot) {
            $context = $this->handleRepresentProperty($property, $annot, $context, $original);
        }

        return $context;
    }

    /**
     * Handles dealing with Represent\Property annotation
     * @param \ReflectionProperty                $property
     * @param Property                           $annot
     * @param \Represent\Context\PropertyContext $context
     * @param                                    $original
     * @return PropertyContext
     */
    private function handleRepresentProperty(\ReflectionProperty $property, Property $annot, PropertyContext $context, $original)
    {
        if ($annot->getName()) {
            $context->name = $annot->getName();
        }

        if ($annot->getType()) {
            $context->value = $this->handleTypeConversion($annot->getType(), $property->getValue($original));
        }

        return $context;
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