<?php
/**
 * Created by IntelliJ IDEA.
 * User: marcusf@patientfocus.com
 * Date: 12/31/14
 * Time: 2:27 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Represent\Builder\Format;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Represent\Builder\ClassContextBuilder;
use Represent\Builder\PropertyContextBuilder;
use Represent\Generator\LinkGenerator;

class DoctrineHalBuilder extends HalFormatBuilder
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(PropertyContextBuilder $propertyBuilder, ClassContextBuilder $classBuilder, array $config = array())
    {
        $this->propertyBuilder = $propertyBuilder;
        $this->classBuilder    = $classBuilder;

        if (!array_key_exists('reader', $config) || !$config['reader'] instanceof AnnotationReader) {
            throw new \Exception('DoctrineBuilder must have a doctrine annotation reader');
        }

        if (!array_key_exists('linkGenerator', $config) || !$config['linkGenerator'] instanceof LinkGenerator) {
            throw new \Exception('DoctrineBuilder must have a link generator');
        }

        if (!array_key_exists('entityManager', $config) || !$config['entityManager'] instanceof EntityManager) {
            throw new \Exception('DoctrineBuilder must have a doctrine entityManager');
        }

        $this->reader        = $config['reader'];
        $this->linkGenerator = $config['linkGenerator'];
        $this->em            = $config['entityManager'];
    }

    protected function handleObject($object, $view)
    {
        if ($object instanceof \Doctrine\ORM\Proxy\Proxy) {
            $object = $this->handleDoctrineProxy($object);
        }

        $check = $this->trackObjectVisits($object);

        if ($check instanceof \stdClass) {

            return $check;
        } else {
            $output = new \stdClass();
            $output->_hash = $check;
        }

        $reflection   = new \ReflectionClass($object);
        $classContext = $this->classBuilder->buildClassContext($reflection, $check, $view);

        foreach ($classContext->properties as $property) {
            $output = $this->handleProperty($property, $classContext, $object, $output);
        }

        $output->_embedded = $this->getEmbedded($output, new \ReflectionClass($object));
        $output->_links    = $this->getLinks(new \ReflectionClass($object), $view);

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
