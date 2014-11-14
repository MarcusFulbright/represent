<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Context\ClassContext;
use Represent\Enum\ExclusionPolicyEnum;

class ClassContextBuilder
{
    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function buildClassContext(\REflectionClass $reflection)
    {
        $context = new ClassContext();
        $context = $this->handleExclusionPolicy($reflection, $context);

        return $context;
    }

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