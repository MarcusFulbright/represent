<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Context\ClassContext;
use Represent\Enum\ExclusionPolicyEnum;

/**
 * Builds ClassContext objects. Handles parsing properties according to exclusion policies.
 */
class ClassContextBuilder
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
     * @return ClassContext
     */
    public function buildClassContext(\ReflectionClass $reflection, $group = null)
    {
        $classContext = new ClassContext();
        $classContext = $this->handleExclusionPolicy($reflection, $classContext);

        $classContext->group = $group;
        $classContext = $this->handleGroup($classContext);

        return $classContext;
    }

    /**
     * Determines exclusion policy and hands off to the appropriate method. Defaults to white list
     * @param \ReflectionClass                $reflection
     * @param \Represent\Context\ClassContext $classContext
     * @return ClassContext
     */
    private function handleExclusionPolicy(\ReflectionClass $reflection, ClassContext $classContext)
    {
        $annot = $this->annotationReader->getClassAnnotation($reflection, '\Represent\Annotations\ExclusionPolicy');

        if (!is_null($annot) && $annot->getPolicy() === ExclusionPolicyEnum::BLACKLIST){
            $classContext->policy = ExclusionPolicyEnum::BLACKLIST;
            $classContext = $this->generatePropertiesForBlackList($reflection, $classContext);
        } else {
            $classContext->policy = ExclusionPolicyEnum::WHITELIST;
            $classContext = $this->generatePropertiesForWhiteList($reflection, $classContext);
        }

        return $classContext;
    }

    /**
     * Decides what properties should be represented using the black list policy
     * @param \ReflectionClass                $reflection
     * @param \Represent\Context\ClassContext $classContext
     * @return ClassContext
     */
    private function generatePropertiesForBlackList(\ReflectionClass $reflection, ClassContext $classContext)
    {
        $properties = $reflection->getProperties();
        $reader     = $this->annotationReader;

        array_walk(
            $properties,
            function ($property) use ($classContext, $reader) {
                $annotation = $reader->getPropertyAnnotation($property, '\Represent\Annotations\Show');
                if ($annotation) {
                    $classContext->properties[] = $property;
                }
            }
        );

        return $classContext;
    }

    /**
     * Decides what properties should be represented using the white list policy
     * @param \ReflectionClass                $reflection
     * @param \Represent\Context\ClassContext $classContext
     * @return ClassContext
     */
    private function generatePropertiesForWhiteList(\ReflectionClass $reflection, ClassContext $classContext)
    {
        $properties = $reflection->getProperties();
        $reader     = $this->annotationReader;

        array_walk(
            $properties,
            function ($property) use ($classContext, $reader) {
                $annotation = $reader->getPropertyAnnotation($property, '\Represent\Annotations\Hide');
                if (!$annotation) {
                    $classContext->properties[] = $property;
                }
            }
        );

        return $classContext;
    }

    /**
     * Takes ClassContext and checks that each property belongs to the given group.
     * @param \Represent\Context\ClassContext $classContext
     * @return ClassContext
     */
    private function handleGroup(ClassContext $classContext)
    {
        $properties = $classContext->properties;
        $reader     = $this->annotationReader;

        $classContext->properties = array_filter(
            $properties,
            function ($property) use ($reader, $classContext) {
                $annotation = $reader->getPropertyAnnotation($property,'\Represent\Annotations\Group');

                return $annotation == null || in_array($classContext->group, $annotation->name);
            }
        );

        return $classContext;
    }
}