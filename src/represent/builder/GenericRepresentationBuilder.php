<?php

namespace represent\builder;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Class GenericRepresentationBuilder
 * @package represent\builder
 *
 * Builds a generic representation of an object that is format agnostic.
 *
 * @author Marcus Fulbright <fulbright.marcus@gmail.com>
 */
class GenericRepresentationBuilder
{
    public function buildRepresentation($object)
    {
        if ($this->checkArrayCollection($object)) {

            $object = $object->toArray();
        }

        if (is_object($object)) {

            $output = $this->handleObject($object);

        } elseif (is_array($object)) {

            $output = $this->handleArray($object);

        } else {

            throw new \Exception('Can only build Representations for objects, arrays, and Doctrine\ArrayCollection');
        }

        return $output;
    }

    private function handleObject($object)
    {
        $reflection = new \ReflectionClass($object);

        return $this->handleProperties($reflection, $object);
    }

    private function handleProperties(\ReflectionClass $reflection, $original)
    {
        $output = new \stdClass();

        foreach ($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            $name  = $property->getName();
            $value = $property->getValue($original);

            if ($this->checkArrayCollection($value)) {

                $value = $value->toArray();
            }

            if (is_array($value)) {

                $output = $this->parseArray($name, $value, $output);

            } elseif(is_object($value)){

                $output->$name = $this->handleObject($value);

            } else {

                $output->$name = $value;

            }
        }

        return $output;
    }

    private function checkArrayCollection($object)
    {
        $class = 'Doctrine\Common\Collections\ArrayCollection';

        return $object instanceof $class;
    }

    private function handleArray(array $object)
    {
        $output = array();

        foreach ($object as $key => $value) {
            if ($this->checkArrayCollection($value)) {
                $value->toArray();
            }

            if (is_array($value)) {

                $output[$key] = $this->handleArray($value);

            } elseif (is_object($value)) {

                $output[$key] = $this->handleObject($value);

            } else{
                $output[$key] = $value;

            }
        }

        return $output;
    }


    private function parseArray($name, array $values, $output)
    {
        $parsed = array();

        foreach ($values as $value) {
            if (is_object($value)){

                $parsed[] = $this->handleObject($value);
            } else {
                $parsed[] = $value;
            }
        }
        if ($name) {
            $output->$name = $parsed;

            return $output;
        }

        return $output;
    }
}

