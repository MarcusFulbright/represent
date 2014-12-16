<?php

namespace Represent\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Represent\Instantiator\GenericInstantiator;
use Represent\Instantiator\InstantiatorInterface;

class DoctrineDeserializer
{
    private $genericInstantiator;

    private $customInstantiator;

    public function __construct(
        InstantiatorInterface $customInstantiator = null,
        GenericInstantiator $genericInstantiator,
        EntityManager $em)
    {
        $this->customInstantiator  = $customInstantiator;
        $this->genericInstantiator = $genericInstantiator;
        $this->em                  = $em;
    }

    public function deSerialize($data, $class, $format)
    {
        switch ($format):
            case 'json':
                return $this->handleJson($data, $class);
            default:
                throw new \Exception('Cannot determine how to de-serialize: '.$format);
        endswitch;
    }

    private function handleJson($data, $class)
    {
        $data   = json_decode($data);
        $output = $this->fromStdObject($data, $class);

        return $output;
    }

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
     * @todo make sure that this respects the type
     * @param                  $data
     * @param                  $object
     * @param \ReflectionClass $reflection
     * @param                  $fields
     */
    private function handleProperties($data, $object, \ReflectionClass $reflection, $fields)
    {
        foreach ($fields as $field) {
            $property = $reflection->getProperty($field);
            $property->setAccessible(true);
            $name = $property->getName();
            $property->setValue($object, $data->$name);
        }
    }

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

    private function getReflection($class)
    {
        $reflection = new \ReflectionClass($class);

        if ($reflection->isAbstract()) {
            throw new \Exception('Represent cannot instantiate abstract class: '.$class);
        }

        return $reflection;
    }
}