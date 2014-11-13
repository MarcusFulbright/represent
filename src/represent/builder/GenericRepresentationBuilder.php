<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Enum\PropertyTypeEnum;
use Represent\Factory\MetaDataFactory;

/**
 * Builds a generic representation of an object that is format agnostic.
 *
 * @author Marcus Fulbright <fulbright.marcus@gmail.com>
 */
class GenericRepresentationBuilder
{
    /**
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    private $reader;

    /**
     * @var \Represent\Factory\MetaDataFactory
     */
    private $factory;

    public function __construct(AnnotationReader $reader, MetaDataFactory $factory)
    {
        $this->reader  = $reader;
        $this->factory = $factory;
    }

    public function buildRepresentation($object)
    {
        switch (true):
            case $this->checkArrayCollection($object):
                $object = $object->toArray();
            case is_array($object):
                $output = $this->handleArray($object);
                break;
            case is_object($object):
                $output = $this->handleObject($object);
                break;
            case is_null($object):
                $output = array();
                break;
            case is_string($object):
                $output = $object;
                break;
            case is_integer($object):
                $output = $object;
                break;
            case is_bool($object):
                $output = $object;
                break;
            default:
                throw new \Exception('Can only build Representations for objects, arrays, null, and Doctrine\ArrayCollection');
            endswitch;

        return $output;
    }

    private function handleObject($object)
    {
        $reflection = new \ReflectionClass($object);
        $output = new \stdClass();

        foreach ($reflection->getProperties() as $property) {
            $output = $this->handleProperty($property, $object, $output);
        }

        return $output;
    }

    private function handleProperty(\ReflectionProperty $property, $original, $output)
    {
        $metaData = $this->factory->propertyMetaFromReflection($property, $original);
        $value    = $metaData->value;
        $name     = $metaData->name;

        switch (true):
            case $this->checkArrayCollection($value):
                $value = $value->toArray();
            case is_array($value);
                $output->$name = $this->handleArray($value);
                break;
            case is_object($value);
                $output->$name = $this->handleObject($value);
                break;
            default:
                $output->$name = $value;
                break;
        endswitch;

        return $output;
    }

    private function checkArrayCollection($object)
    {
        $class = 'Doctrine\Common\Collections\ArrayCollection';

        return $object instanceof $class;
    }

    private function handleArray(array $object)
    {
        $output   = array();
        foreach ($object as $key => $value) {
            switch (true):;
                case is_array($value):
                    $output[$key] = $this->handleArray($value);
                    break;
                case is_object($value):
                    $output[$key] = $this->handleObject($value);
                    break;
                default:
                    $output[$key] = $value;
            endswitch;
        }

        return $output;
    }
}

