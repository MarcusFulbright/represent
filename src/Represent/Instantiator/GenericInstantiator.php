<?php

namespace Represent\Instantiator;

class GenericInstantiator
{
    /**
     * Returns a new instance of a class based on the given reflection class and the given stdClass data object
     * To avoid weird errors, always have your code run supports first
     *
     * @param \stdClass        $data
     * @param \ReflectionClass $reflection
     * @return object
     */
    public function instantiate(\stdClass $data, \ReflectionClass $reflection)
    {
        $object = $reflection->newInstanceArgs($this->prepareConstructorArguments($reflection->getConstructor(), $data));

        return $object;
    }

    /**
     * Determines if this class can instantiate an object from the given data
     *
     * @param \stdClass        $data
     * @param \ReflectionClass $reflection
     * @return bool
     */
    public function supports(\stdClass $data, \ReflectionClass $reflection)
    {
        $constructor       = $reflection->getConstructor();
        $constructorParams = $constructor->getParameters();

        $invalidParams = array_filter(
            $constructorParams,
            function($param) use ($data) {
                $name = $param->getName();
                if ($param->isDefaultValueAvailable()|| property_exists($data, $name)) {
                    return false;
                } else {
                    return true;
                }
            }
        );

        return count($invalidParams) > 0 ? false : true;
    }


    private function prepareConstructorArguments(\ReflectionMethod $constructor, \stdClass $data)
    {
        $output = array();
        $params = $constructor->getParameters();

        foreach ($params as $param) {
            $name     = $param->getName();
            $value    = property_exists($data, $name) ? $data->$name : $param->getDefaultValue();
            $output[] = $value;
        }

        return $output;
    }
}