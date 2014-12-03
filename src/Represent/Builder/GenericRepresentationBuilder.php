<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use MyProject\Proxies\__CG__\stdClass;
use Represent\Builder\ClassContextBuilder;
use Represent\Context\ClassContext;

/**
 * Builds a generic representation of an object that is format agnostic.
 *
 * @author Marcus Fulbright <fulbright.marcus@gmail.com>
 */
class GenericRepresentationBuilder
{
    /**
     * @var \Represent\Builder\PropertyContextBuilder
     */
    private $propertyBuilder;

    /**
     * @var ClassContextBuilder
     */
    private $classBuilder;

    /**
     * @var array
     */
    private $visited = array();

    public function __construct(PropertyContextBuilder $propertyBuilder, ClassContextBuilder $classBuilder)
    {
        $this->propertyBuilder = $propertyBuilder;
        $this->classBuilder    = $classBuilder;
    }

    /**
     * Entry point to build a generic representation. Will handle parse an object and return only stdClass, and arrays
     * with keys and values
     *
     * @param $object
     * @param string $view name of the view to be represented
     * @return array|\stdClass
     * @throws \Exception
     */
    public function buildRepresentation($object, $view = null)
    {
        switch (true):
            case $this->checkArrayCollection($object):
                $object = $object->toArray();
            case is_array($object):
                $output = $this->handleArray($object, $view);
                break;
            case is_object($object):
                $output = $this->handleObject($object, $view);
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
     * @param $view
     * @return \stdClass
     */
    private function handleObject($object, $view)
    {
        $hash   = spl_object_hash($object);
        $check  = array_search($hash, $this->visited);
        $output = new \stdClass();

        if ($check !== false) {
            $output = new \stdClass();
            $rel = '$rel';
            $output->$rel = $check;

            return $output;
        }
        $output->_hash = count($this->visited);
        $this->visited[] = $hash;
        $reflection      = new \ReflectionClass($object);
        $classContext    = $this->classBuilder->buildClassContext($reflection, $hash, $view);

        foreach ($classContext->properties as $property) {
            $output = $this->handleProperty($property, $object, $output, $classContext);
        }

        return $output;
    }

    /**
     * Used to handle determining representing object properties
     * @param \ReflectionProperty             $property
     * @param                                 $original
     * @param                                 $output
     * @param \Represent\Context\ClassContext $classContext
     * @return stdClass
     */
    private function handleProperty(\ReflectionProperty $property, $original, $output, ClassContext $classContext)
    {
        $propertyContext = $this->propertyBuilder->propertyContextFromReflection($property, $original, $classContext);
        $value    = $propertyContext->value;
        $name     = $propertyContext->name;

        switch (true):
            case $this->checkArrayCollection($value):
                $value = $value->toArray();
            case is_array($value);
                $output->$name = $this->handleArray($value, $classContext->views);
                break;
            case is_object($value);
                $output->$name = $this->handleObject($value, $classContext->views);
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
     * @param string $view
     * @return array
     */
    private function handleArray(array $object, $view)
    {
        $output = array();
        foreach ($object as $key => $value) {
            switch (true):;
                case is_array($value):
                    $output[$key] = $this->handleArray($value, $view);
                    break;
                case is_object($value):
                    $output[$key] = $this->handleObject($value, $view);
                    break;
                default:
                    $output[$key] = $value;
            endswitch;
        }

        return $output;
    }
}

