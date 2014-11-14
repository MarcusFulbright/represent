<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\MetaData\ClassMetaData;
use Represent\Enum\ExclusionPolicyEnum;

/**
 * Builds ClassMetaData objects. Handles parsing properties according to exclusion policies.
 */
class ClassMetaDataBuilder
{
    /**
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    private $annotationReader;

    /**
     * @param AnnotationReader $annotationReader
     */
    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Entry point to build the class context object. Currently only knows how to handle exclusion policy.
     * Any new top level class annotations need a handler in here.
     *
     * @param \ReflectionClass $reflection
     * @param                  $group
     * @return array|ClassMetaData
     */
    public function buildClassMetaData(\ReflectionClass $reflection, $group)
    {
        $classMeta = new ClassMetaData();
        $classMeta = $this->handleExclusionPolicy($reflection, $classMeta);

        if ($group) {
            $classMeta->group = $group;
            $classMeta = $this->handleGroup($classMeta);
        }

        return $classMeta;
    }

    /**
     * Determines exclusion policy and hands off to the appropriate method. Defaults to white list
     *
     * @param \ReflectionClass $reflection
     * @param ClassMetaData     $classMeta
     * @return array|ClassMetaData
     */
    private function handleExclusionPolicy(\ReflectionClass $reflection, ClassMetaData $classMeta)
    {
        $annot = $this->annotationReader->getClassAnnotation($reflection, '\Represent\Annotations\ExclusionPolicy');

        if (!is_null($annot) && $annot->getPolicy() === ExclusionPolicyEnum::BLACKLIST){
            $classMeta->policy = ExclusionPolicyEnum::BLACKLIST;
            $classMeta = $this->generatePropertiesForBlackList($reflection, $classMeta);
        } else {
            $classMeta->policy = ExclusionPolicyEnum::WHITELIST;
            $classMeta = $this->generatePropertiesForWhiteList($reflection, $classMeta);
        }

        return $classMeta;
    }

    /**
     * Decides what properties should be represented using the black list policy
     *
     * @param \ReflectionClass $reflection
     * @param ClassMetaData     $classMeta
     * @return array|ClassMetaData
     */
    private function generatePropertiesForBlackList(\ReflectionClass $reflection, ClassMetaData $classMeta)
    {
        array_walk(
            $reflection->getProperties(),
            function ($property) use ($classMeta) {
                $annotation = $this->annotationReader->getPropertyAnnotation($property, '\Represent\Annotations\Show');
                if ($annotation) {
                    $classMeta->properties[] = $property;
                }
            }
        );

        return $classMeta;
    }

    /**
     * Decides what properties should be represented using the white list policy
     *
     * @param \ReflectionClass $reflection
     * @param ClassMetaData     $classMeta
     * @return ClassMetaData
     */
    private function generatePropertiesForWhiteList(\ReflectionClass $reflection, ClassMetaData $classMeta)
    {
        $properties = $reflection->getProperties();
        $reader     = $this->annotationReader;

        array_walk(
            $properties,
            function ($property) use ($classMeta, $reader) {
                $annotation = $reader->getPropertyAnnotation($property, '\Represent\Annotations\Hide');
                if (!$annotation) {
                    $classMeta->properties[] = $property;
                }
            }
        );

        return $classMeta;
    }

    /**
     * Takes a ClassMetaData and checks that each property belongs to the given group.
     *
     * @param ClassMetaData $classMeta
     * @return ClassMetaData
     */
    private function handleGroup(ClassMetaData $classMeta)
    {
        $properties = $classMeta->properties;
        $reader     = $this->annotationReader;

        $classMeta->properties = array_filter(
            $properties,
            function ($property) use ($reader, $classMeta) {
                $annotation  = $reader->getPropertyAnnotation($property,'\Represent\Annotations\Group');
                $annotation != null && in_array($classMeta->group, $annotation->name);
            }
        );

        return $classMeta;
    }
}