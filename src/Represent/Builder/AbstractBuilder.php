<?php

namespace Represent\Builder;

use Represent\Context\ClassContext;

abstract class AbstractBuilder
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

    /**
     * The contextBuilder's are essential for traversing objects and must always be included. An optional $config array
     * can house any extras that you might want to inject for specific implementations.
     * @param PropertyContextBuilder $propertyBuilder
     * @param ClassContextBuilder $classBuilder
     * @param array $config
     */
    abstract public function __construct(PropertyContextBuilder $propertyBuilder, ClassContextBuilder $classBuilder, array $config = array());

    /**
     * Dictates how the building handles traversing objects, either as root or as a value(s) of a property. Can make use
     * of the ClassContextBuilder to respect the given representation view. Should also use trackObjectVisits to ensure
     * that an object is only serialized once to increase efficiency and prevent circular references.
     * @param $object
     * @param $view
     * @return mixed
     */
    abstract protected function handleObject($object, $view);

    /**
     * Determines how to handle an objects properties. Either just takes the value as is, or passes off to handleArray
     * or handleObject appropriately
     * @param \ReflectionProperty $property
     * @param ClassContext $classContext
     * @param $original
     * @param \stdClass $output
     * @return mixed
     */
    abstract protected function handleProperty(\ReflectionProperty $property, ClassContext $classContext, $original, \stdClass $output);

    /**
     * Determines how to handle arrays. Usually either assigns values (preserving keys) or detects objects that can get
     * handed back to handleObject
     * @param array $object
     * @param $view
     * @return mixed
     */
    abstract protected function handleArray(array $object, $view);

    /**
     * Method used to check if an object has already been visited. This implementation assigns every object in a
     * representation a unique hash. In the event of duplicate objects, a key value pair is returned that points to a
     * unique hash. This reduces the size of messages while still including all of the relational meaning behind the
     * data and avoids circular references. Not marked final because this behavior could be changed.
     * @param $object
     * @return mixed
     */
    protected function trackObjectVisits($object)
    {
        $hash   = spl_object_hash($object);
        $check  = array_search($hash, $this->visited);

        if ($check !== false) {
            $output = new \stdClass();
            $rel = '$rel';
            $output->$rel = $check;

            return $output;
        } else {
            $this->visited[] = $hash;

            return count($this->visited);
        }
    }

    /**
     * This method is used to detect doctrine collections.
     * @param $object
     * @return bool
     */
    final protected function checkArrayCollection($object)
    {
        return $object instanceof \Doctrine\Common\Collections\ArrayCollection || $object instanceof \Doctrine\ORM\PersistentCollection;
    }

    /**
     * Entry point to build a generic representation. Will handle parse an object and return only stdClass, and arrays
     * with keys and values. All implementations will use this method as everything will interact with builders through
     * this implementation. The behavior of an implementing class can be altered via the abstract methods. You should
     * not need to alter this.
     * @param $object
     * @param string $view name of the view to be represented
     * @return array|\stdClass
     * @throws \Exception
     */
    final public function buildRepresentation($object, $view = null)
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
                $output = null;
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
}