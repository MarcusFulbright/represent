<?php

namespace Represent\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Annotations\LinkCollection;
use Represent\Generator\LinkGenerator;
use Represent\MetaData\ClassMetaData;

class HalBuilder
{
    /**
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    private $reader;

    /**
     * @var \Represent\Generator\LinkGenerator
     */
    private $linkGenerator;

    /**
     * @param AnnotationReader $reader
     * @param LinkGenerator    $linkGenerator
     */
    public function __construct(AnnotationReader $reader, LinkGenerator $linkGenerator)
    {
        $this->reader        = $reader;
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * Adds the _embedded and _links property to a generic representation
     *
     * @param               $representation
     * @param               $object
     * @param ClassMetaData $meta
     * @return mixed
     */
    public function buildHalRepresentation($representation, $object, ClassMetaData $meta)
    {
        $representation->_embedded = $this->getEmbedded($representation, $object, new \ReflectionClass($object));
        $representation->_links    = $this->getLinks(new \ReflectionClass($object), $meta);

        return $representation;
    }

    /**
     * Handles moving embedded properties to _embedded
     * @param                  $representation
     * @param                  $object
     * @param \ReflectionClass $reflection
     * @return \stdClass
     */
    private function getEmbedded($representation, $object, \ReflectionClass $reflection)
    {
        $embedded = new \stdClass();

        foreach ($reflection->getProperties() as $property) {
            $annot = $this->reader->getPropertyAnnotation($property, '\Represent\Annotations\HalEmbedded');

            if ($annot) {
                $name  = $property->getName();
                $value = $property->getValue($object);

                $embedded->$name = $value;
                unset($representation->$name);
            }
        }

        return $embedded;
    }

    /**
     * Handles getting _links
     *
     * @param               $reflection
     * @param ClassMetaData $meta
     *
     * @return \stdClass
     */
    private function getLinks(\ReflectionClass $reflection, ClassMetaData $meta)
    {
        $links = new \stdClass();
        $annot = $this->reader->getClassAnnotation($reflection, '\Represent\Annotations\LinkCollection');

        if ($annot) {
            $links = $this->parseLinks($annot, $meta, $links);
        }

        return $links;
    }

    /**
     * Parses through link annotations and generates valid links
     *
     * @param LinkCollection $annot
     * @param ClassMetaData  $meta
     * @param \stdClass      $output
     * @return \stdClass
     */
    private function parseLinks(LinkCollection $annot, ClassMetaData $meta, \stdClass $output)
    {
        foreach ($annot->links as $link) {
            if ($meta->group && $meta->group != $link->group) {
                break;
            }
            $name          = $link->name;
            $output->$name = $this->linkGenerator->generate($link);
        }

        return $output;
    }
}