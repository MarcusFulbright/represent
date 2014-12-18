<?php

namespace Represent\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Represent\Handler\PropertyHandler;
use Represent\Instantiator\GenericInstantiator;
use Represent\Instantiator\InstantiatorInterface;

class DoctrineDeserializer
{
    /**
     * @var \Represent\Instantiator\GenericInstantiator
     */
    private $genericInstantiator;

    /**
     * @var \Represent\Instantiator\InstantiatorInterface
     */
    private $customInstantiator;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Represent\Handler\PropertyHandler
     */
    private $propertyHandler;

    /**
     * @param InstantiatorInterface $customInstantiator
     * @param GenericInstantiator $genericInstantiator
     * @param EntityManager $em
     * @param PropertyHandler $propertyHandler
     */
    public function __construct(
        InstantiatorInterface $customInstantiator = null,
        GenericInstantiator $genericInstantiator,
        EntityManager $em,
        PropertyHandler $propertyHandler)
    {
        $this->customInstantiator  = $customInstantiator;
        $this->genericInstantiator = $genericInstantiator;
        $this->propertyHandler     = $propertyHandler;
        $this->em                  = $em;
    }

    /**
     * Entry point to De-Serialize a supported format into a class
     *
     * @param $data
     * @param $class
     * @param $format
     * @return object
     * @throws \Exception
     */
    public function deSerialize($data, $class, $format)
    {
        switch ($format):
            case 'json':
                return $this->handleJson($data, $class);
            default:
                throw new \Exception('Cannot determine how to de-serialize: '.$format);
        endswitch;
    }

    /**
     * Handles json
     *
     * @param $data
     * @param $class
     * @return object
     */
    private function handleJson($data, $class)
    {
        $data   = json_decode($data);
        $output = $this->fromStdObject($data, $class);

        return $output;
    }

    /**
     * Converts a stdClass representation of data into a class
     *
     * @param \stdClass $data
     * @param $class
     * @return object
     */
    private function fromStdObject(\stdClass $data, $class)
    {
        $reflection   = $this->getReflection($class);
        $object       = $this->getClassInstance($data, $reflection);
        $map          = $this->getPropertyMap($class);
        $this->handleAssociations($data, $object, $reflection, $map['associations'], $map['meta']);
        $this->handleProperties($data, $object, $reflection, $map['fields']);

        return $object;
    }

    /**
     * Handles associations
     *
     * @param                                         $data
     * @param                                         $object
     * @param \ReflectionClass                        $reflection
     * @param                                         $associations
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $meta
     * @throws \Exception
     */
    private function handleAssociations($data, $object, \ReflectionClass $reflection, $associations, ClassMetadataInfo $meta)
    {
        foreach ($associations as $association) {
            $mapping  = $meta->getAssociationMapping($association);
            $property = $reflection->getProperty($association);
            $property->setAccessible(true);

            if (!property_exists($data, $association)) {
                continue;
            }

            switch (true):
                case $meta->isSingleValuedAssociation($association):
                    $value = $this->fromStdObject($data->$association, $mapping['targetEntity']);
                    $property->setValue($object, $value);
                    break;
                case $meta->isCollectionValuedAssociation($association):
                    $collection = new ArrayCollection();

                    foreach ($data->$association as $relatedObject) {
                        $collection->add($this->fromstdobject($relatedObject, $mapping['targetEntity']));
                    }
                    $property->setValue($object, $collection);
                    break;
                default:
                    throw new \Exception('Represent cannot determine how to traverse relationship');
            endswitch;
        }
    }

    /**
     * Handles properties
     *
     * @param                  $data
     * @param                  $object
     * @param \ReflectionClass $reflection
     * @param                  $fields
     */
    private function handleProperties($data, $object, \ReflectionClass $reflection, $fields)
    {
        foreach ($fields as $field) {
            $property = $reflection->getProperty($field);
            $name     = $property->getName();

            if (property_exists($data, $name) === false ) {
                continue;
            }
            $property->setAccessible(true);
            $type  = $this->propertyHandler->propertyTypeOverride(null, $property);
            $value = $this->propertyHandler->handleTypeConversion($type, $data->$name);

            $property->setValue($object, $value);
        }
    }

    /**
     * builds a property map for doctrine entities
     *
     * @param $class
     * @return mixed
     */
    private function getPropertyMap($class)
    {
        $output['meta'] = $meta = $this->em->getClassMetadata($class);
        $output['fields']       = $meta->getFieldNames();
        $output['associations'] = $meta->getAssociationNames();

        $output['fields'] = array_filter(
            $output['fields'],
            function($fieldName) use ($output) {
                if (in_array($fieldName, $output['associations'])) {
                    return false;
                } else {
                    return true;
                }
            }
        );

        return $output;
    }

    /**
     * Instantiates a class from a reflection. uses $data to navigate constructors
     *
     * @param \stdClass $data
     * @param \ReflectionClass $reflection
     * @return object
     * @throws \Exception
     */
    private function getClassInstance(\stdClass $data, \ReflectionClass $reflection)
    {
        switch (true):
            case $this->genericInstantiator->supports($data, $reflection):
                $output = $this->genericInstantiator->instantiate($data, $reflection);
                break;
            case $this->customInstantiator->supports($data, $reflection):
                $output = $this->customInstantiator->instantiate($data, $reflection);
                break;
            default:
                throw new \Exception('Cannot determine how to instantiate object of class: '.$reflection->getParentClass());
        endswitch;

        return $output;
    }

    /**
     * Get a reflection for $class
     *
     * @param $class
     * @return \ReflectionClass
     * @throws \Exception
     */
    private function getReflection($class)
    {
        $reflection = new \ReflectionClass($class);

        if ($reflection->isAbstract()) {
            throw new \Exception('Represent cannot instantiate abstract class: '.$class);
        }

        return $reflection;
    }
}
