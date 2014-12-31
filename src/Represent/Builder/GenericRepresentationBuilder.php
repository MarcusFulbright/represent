<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use MyProject\Proxies\__CG__\stdClass;
use Represent\Builder\ClassContextBuilder;
use Represent\Context\ClassContext;

/**
 * Builds a generic representation of an object that is format agnostic.
 *
 * @author Marcus Fulbright <fulbright.marcus@gmail.com>
 */
class GenericRepresentationBuilder extends AbstractBuilder
{
    /**
     * @var \Represent\Builder\PropertyContextBuilder
     */
    protected $propertyBuilder;

    /**
     * @var ClassContextBuilder
     */
    protected $classBuilder;

    /**
     * @var array
     */
    protected $visited = array();

    public function __construct(PropertyContextBuilder $propertyBuilder, ClassContextBuilder $classBuilder, array $config = array())
    {
        $this->propertyBuilder = $propertyBuilder;
        $this->classBuilder    = $classBuilder;
    }

    /**
     * Used to handle representing objects
     * @param $object
     * @param $view
     * @return \stdClass
     */
    protected function handleObject($object, $view)
    {
        $check = $this->trackObjectVisits($object);

        if ($check instanceof \stdClass) {

            return $check;
        } else {
            $output = new \stdClass();
            $output->_hash = $check;
        }

        $reflection   = new \ReflectionClass($object);
        $classContext = $this->classBuilder->buildClassContext($reflection, $check, $view);

        foreach ($classContext->properties as $property) {
            $output = $this->handleProperty($property, $classContext, $object, $output);
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
    protected function handleProperty(\ReflectionProperty $property, ClassContext $classContext, $original, \stdClass $output)
    {
        $propertyContext = $this->propertyBuilder->propertyContextFromReflection($property, $original, $classContext);
        $value           = $propertyContext->value;
        $name            = $propertyContext->name;
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
     * Can handle representing an array
     *
     * @param array $object
     * @param string $view
     * @return array
     */
    protected function handleArray(array $object, $view)
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

