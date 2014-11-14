<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Context\ClassContext;
use Represent\Enum\ExclusionPolicyEnum;

/**
 * Builds class context objects. Handles parsing properties according to exclusion policies.
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
     * @return array|ClassContext
     */
    public function buildClassContext(\ReflectionClass $reflection)
    {
        $context = new ClassContext();
        $context = $this->handleExclusionPolicy($reflection, $context);

        return $context;
    }

    /**
     * Determines exclusion policy and hands off to the appropriate method. Defaults to white list
     *
     * @param \ReflectionClass $reflection
     * @param ClassContext     $context
     * @return array|ClassContext
     */
    private function handleExclusionPolicy(\ReflectionClass $reflection, ClassContext $context)
    {
        $annot = $this->annotationReader->getClassAnnotation($reflection, '\Represent\Annotations\ExclusionPolicy');

        if (!is_null($annot) && $annot->getPolicy() === ExclusionPolicyEnum::BLACKLIST){
            $context->policy = ExclusionPolicyEnum::BLACKLIST;
            $context = $this->generatePropertiesForBlackList($reflection, $context);
        } else {
            $context->policy = ExclusionPolicyEnum::WHITELIST;
            $context = $this->generatePropertiesForWhiteList($reflection, $context);
        }

        return $context;
    }

    /**
     * Decides what properties should be represented using the black list policy
     *
     * @param \ReflectionClass $reflection
     * @param ClassContext     $context
     * @return array|ClassContext
     */
    private function generatePropertiesForBlackList(\ReflectionClass $reflection, ClassContext $context)
    {
        foreach ($reflection->getProperties() as $property) {
            foreach ($this->annotationReader->getPropertyAnnotations($property) as $annot) {
                if ($annot instanceof \Represent\Annotations\Show) {
                    $context[] = $property;
                }
            }
        }

        return $context;
    }

    /**
     * Decides what properties should be represented using the white list policy
     *
     * @param \ReflectionClass $reflection
     * @param ClassContext     $context
     * @return ClassContext
     */
    private function generatePropertiesForWhiteList(\ReflectionClass $reflection, ClassContext $context)
    {
        foreach ($reflection->getProperties() as $property) {
            $annotations = $this->annotationReader->getPropertyAnnotations($property);

            if (empty($annotations)) {
                $context->properties[] = $property;
            } else {
                foreach($annotations as $annot) {
                    if (!$annot instanceof \Represent\Annotations\Hide) {
                        $context->properties[] = $property;
                    }
                }
            }
        }

        return $context;
    }
}