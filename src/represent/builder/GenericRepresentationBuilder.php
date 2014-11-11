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
        $output = array();

        foreach ($reflection->getProperties() as $property)
        {
            $property->setAccessible(true);
            $output[$property->getName()] = $property->getValue($original);
        }

        return $output;
    }
}

