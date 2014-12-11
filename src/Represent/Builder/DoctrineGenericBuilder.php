<?php

namespace Represent\Builder;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

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
            $object = $this->handleDoctrineProxy($object);
        }

        $hash   = spl_object_hash($object);
        $check  = array_search($hash, $this->visited);
        $output = new \stdClass();

        if ($check !== false) {
            $output = new \stdClass();
            $rel    = '$rel';
            $output->$rel = $check;

            return $output;
        }
        $output->_hash = count($this->visited);
        $this->visited[] = $hash;
        $reflection      = new \ReflectionClass($object);
        $classContext    = $this->classBuilder->buildClassContext($reflection, $hash, $view);

        foreach ($classContext->properties as $property) {
            $output = $this->handleProperty($property, $object, $output, $classContext);
        }

        return $output;
    }

    private function handleDoctrineProxy(\Doctrine\ORM\Proxy\Proxy $proxy)
    {
        do {
            $this->em->detach($proxy);
            $object = $this->em->find(get_class($proxy), $proxy->getPrimaryKey());
            $this->em->merge($object);
        } while ($object instanceof \Doctrine\ORM\Proxy\Proxy);

        return $object;
    }
}