<?php

namespace Represent\Builder\Format;

use Doctrine\Common\Annotations\AnnotationReader as reader;
use Represent\Annotations\LinkCollection;
use Represent\Builder\AbstractBuilder;
use Represent\Builder\ClassContextBuilder;
use Represent\Builder\PropertyContextBuilder;
use Represent\Context\ClassContext;
use Represent\Generator\LinkGenerator as generator;

class HalFormatBuilder extends AbstractBuilder
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
     * {@inheritdoc}
     */
    public function __construct(PropertyContextBuilder $propertyBuilder, ClassContextBuilder $classBuilder, array $config = array())
    {
        $this->propertyBuilder = $propertyBuilder;
        $this->classBuilder    = $classBuilder;

        if (!array_key_exists('reader', $config) || !$config['reader'] instanceof reader) {
            throw new \Exception('HalFormatBuilder must have a doctrine annotation reader');
        }

        if (!array_key_exists('linkGenerator', $config) || !$config['linkGenerator'] instanceof generator) {
            throw new \Exception('HalFormatBuilder must have a link generator');
        }

        $this->reader        = $config['reader'];
        $this->linkGenerator = $config['linkGenerator'];
    }

    /**
     * Used to handle representing objects
     * @param $object
     * @param $view
     * @return \stdClass
     */
    protected function handleObject($object, $view)
    {
        $check = $this->trackObjectVisits($object);

        if ($check instanceof \stdClass){

            return $check;
        } else {
            $output = new \stdClass();
            $output->_hash = $check;
        }

        $reflection      = new \ReflectionClass($object);
        $classContext    = $this->classBuilder->buildClassContext($reflection, $check, $view);

        foreach ($classContext->properties as $property) {
            $output = $this->handleProperty($property, $classContext, $object, $output);
        }

        $output->_embedded = $this->getEmbedded($output, new \ReflectionClass($object));
        $output->_links    = $this->getLinks(new \ReflectionClass($object), $view);

        return $output;
    }

    /**
     * Used to handle determining representing object properties
     * @param \ReflectionProperty             $property
     * @param                                 $original
     * @param                                 $output
     * @param \Represent\Context\ClassContext $classContext
     * @return stdClass
     */
    protected function handleProperty(\ReflectionProperty $property, ClassContext $classContext, $original, \stdClass $output)
    {
        $propertyContext = $this->propertyBuilder->propertyContextFromReflection($property, $original, $classContext);
        $value           = $propertyContext->value;
        $name            = $propertyContext->name;
        switch (true):
            case $this->checkArrayCollection($value):
                $value = $value->toArray();
            case is_array($value);
                $output->$name = $this->handleArray($value, $classContext->views);
                break;
            case is_object($value);
                $output->$name = $this->handleObject($value, $classContext->views);
                break;
            default:
                $output->$name = $value;
                break;
        endswitch;

        return $output;
    }

    /**
     * Can handle representing an array
     *
     * @param array $object
     * @param string $view
     * @return array
     */
    protected function handleArray(array $object, $view)
    {
        $output = array();
        foreach ($object as $key => $value) {
            switch (true):;
                case is_array($value):
                    $output[$key] = $this->handleArray($value, $view);
                    break;
                case is_object($value):
                    $output[$key] = $this->handleObject($value, $view);
                    break;
                default:
                    $output[$key] = $value;
            endswitch;
        }

        return $output;
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