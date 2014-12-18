<?php

namespace Represent\Builder;

use Represent\Annotations\Property;
use Represent\Context\PropertyContext;
use Represent\Handler\PropertyHandler;

/**
 * Responsible for building representations of class properties
 */
class PropertyContextBuilder
{
    private $propertyHandler;

    public function __construct(PropertyHandler $propertyhandler)
    {
        $this->propertyHandler = $propertyhandler;
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
        $annot = $this->propertyHandler->getPropertyAnnotation($property);

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
            $context->value = $this->propertyHandler->handleTypeConversion($annot->getType(), $property->getValue($original));
        }

        return $context;
    }
}