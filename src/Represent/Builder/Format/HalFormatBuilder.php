<?php

namespace Represent\Builder\Format;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Annotations\LinkCollection;
use Represent\Generator\LinkGenerator;

class HalFormatBuilder implements  FormatBuilderInterface
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
     * @param      $representation
     * @param      $object
     * @param null $view
     * @return mixed
     */
    public function buildRepresentation($representation, $object, $view = null)
    {
        $representation->_embedded = $this->getEmbedded($representation, new \ReflectionClass($object));
        $representation->_links    = $this->getLinks(new \ReflectionClass($object), $view);

        return $representation;
    }

    /**
     * Handles moving embedded properties to _embedded
     * @param                  $representation
     * @param \ReflectionClass $reflection
     * @return \stdClass
     */
    private function getEmbedded($representation, \ReflectionClass $reflection)
    {
        $embedded   = new \stdClass();
        $properties = $reflection->getProperties();
        $reader     = $this->reader;

        array_walk(
            $properties,
            function (\ReflectionProperty $property) use ($representation, $embedded, $reader) {
                $property->setAccessible(true);
                $annot = $reader->getPropertyAnnotation($property, '\Represent\Annotations\Embedded');

                if ($annot) {
                    $name  = $property->getName();
                    $value = $representation->$name;
                    $embedded->$name = $value;
                    unset($representation->$name);
                }
            }
        );

        return $embedded;
    }

    /**
     * Handles getting _links
     * @param \ReflectionClass $reflection
     * @param                  $view
     * @return \stdClass
     */
    private function getLinks(\ReflectionClass $reflection, $view)
    {
        $links = new \stdClass();
        $annot = $this->reader->getClassAnnotation($reflection, '\Represent\Annotations\LinkCollection');

        if ($annot) {
            $links = $this->parseLinks($annot, $view, $links);
        }

        return $links;
    }

    /**
     * Parses through link annotations and generates valid links
     * @param LinkCollection $annot
     * @param                $view
     * @param \stdClass      $output
     * @return \stdClass
     */
    private function parseLinks(LinkCollection $annot, $view, \stdClass $output)
    {
        $generator = $this->linkGenerator;
        array_walk(
            $annot->links,
            function($link) use ($view, $output, $generator) {
                if ($view == null || in_array($view, $link->views)) {
                    $name = $generator->parseName($link);
                    $output->$name = $generator->generate($link);
                }
            }
        );

        return $output;
    }
}