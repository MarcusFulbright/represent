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
    public function buildObjectRepresentation($object)
    {
        if (!is_object($object)) {
            throw new \Exception('Trying to build a representation for a non-object');
        }

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

                $output = $this->handleArray($name, $value, $output);

            } elseif(is_object($value)){

                $output->$name = $this->buildObjectRepresentation($value);

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

    private function handleArray($name, array $values, $output)
    {
        $parsed = array();

        foreach ($values as $value) {
            if (is_object($value)){

                $parsed[] = $this->buildObjectRepresentation($value);
            } else {
                $parsed[] = $value;
            }
        }
        $output->$name = $parsed;

        return $output;
    }
}

