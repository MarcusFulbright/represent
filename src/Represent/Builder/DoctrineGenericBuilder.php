<?php

namespace Represent\Builder;

use Doctrine\ORM\EntityManager;

class DoctrineGenericBuilder extends GenericRepresentationBuilder
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(PropertyContextBuilder $propertyBuilder, ClassContextBuilder $classBuilder, EntityManager $em)
    {
        $this->propertyBuilder = $propertyBuilder;
        $this->classBuilder    = $classBuilder;
        $this->em              = $em;
    }

    protected function handleObject($object, $view)
    {
        if ($object instanceof \Doctrine\ORM\Proxy\Proxy) {
            $object = $this->em->find(get_class($object), $object->getPrimaryKey());
        }

        return parent::handleObject($object, $view);
    }
}