<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Builder\ClassMetaDataBuilder;
use Represent\MetaData\ClassMetaData;

/**
 * Builds a generic representation of an object that is format agnostic.
 *
 * @author Marcus Fulbright <fulbright.marcus@gmail.com>
 */
class GenericRepresentationBuilder
{
    /**
     * @var \Represent\Builder\PropertyMetaDataBuilder
     */
    private $propertyBuilder;

    /**
     * @var ClassMetaDataBuilder
     */
    private $classBuilder;

    public function __construct(PropertyMetaDataBuilder $propertyBuilder, ClassMetaDataBuilder $classBuilder)
    {
        $this->propertyBuilder = $propertyBuilder;
        $this->classBuilder = $classBuilder;
    }

    /**
     * Entry point to build a generic representation. Will handle parse an object and return only stdClass, and arrays
     * with keys and values
     *
     * @param $object
     * @param string $group name of the group to be represented
     * @return array|\stdClass
     * @throws \Exception
     */
    public function buildRepresentation($object, $group = null)
    {
        switch (true):
            case $this->checkArrayCollection($object):
                $object = $object->toArray();
            case is_array($object):
                $output = $this->handleArray($object, $group);
                break;
            case is_object($object):
                $output = $this->handleObject($object, $group);
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
                throw new \Exception('Can not determine how to build representation');
            endswitch;

        return $output;
    }

    /**
     * Used to handle representing objects
     * @param $object
     * @param $group
     * @return \stdClass
     */
    private function handleObject($object, $group)
    {
        $reflection = new \ReflectionClass($object);
        $classMeta    = $this->classBuilder->buildClassMetaData($reflection, $group);
        $output     = new \stdClass();

        foreach ($classMeta->properties as $property) {
            $output = $this->handleProperty($property, $object, $output, $classMeta);
        }

        return $output;
    }

    /**
     * Used to handle determining representing object properties
     *
     * @param \ReflectionProperty $property
     * @param $original
     * @param $output
     * @param ClassMetaData $classMeta
     * @return mixed
     */
    private function handleProperty(\ReflectionProperty $property, $original, $output, ClassMetaData $classMeta)
    {
        $metaData = $this->propertyBuilder->propertyMetaFromReflection($property, $original, $classMeta);
        $value    = $metaData->value;
        $name     = $metaData->name;

        switch (true):
            case $this->checkArrayCollection($value):
                $value = $value->toArray();
            case is_array($value);
                $output->$name = $this->handleArray($value, $classMeta->group);
                break;
            case is_object($value);
                $output->$name = $this->handleObject($value, $classMeta->group);
                break;
            default:
                $output->$name = $value;
                break;
        endswitch;

        return $output;
    }

    /**
     * Returns true if object is an instanceof array collection
     *
     * @param $object
     * @return bool
     */
    private function checkArrayCollection($object)
    {
        $class = 'Doctrine\Common\Collections\ArrayCollection';

        return $object instanceof $class;
    }

    /**
     * Can handle representing an array
     *
     * @param array $object
     * @param string $group
     * @return array
     */
    private function handleArray(array $object, $group)
    {
        $output = array();
        foreach ($object as $key => $value) {
            switch (true):;
                case is_array($value):
                    $output[$key] = $this->handleArray($value, $group);
                    break;
                case is_object($value):
                    $output[$key] = $this->handleObject($value, $group);
                    break;
                default:
                    $output[$key] = $value;
            endswitch;
        }

        return $output;
    }
}
