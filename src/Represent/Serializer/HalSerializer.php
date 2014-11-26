<?php

namespace Represent\Serializer;

use Represent\Builder\Format\FormatBuilderInterface;
use Represent\Builder\GenericRepresentationBuilder;

class HalSerializer implements RepresentSerializerInterface
{
    /**
     * @var \Represent\Builder\Format\FormatBuilderInterface
     */
    private $formatBuilder;

    /**
     * @var \Represent\Builder\GenericRepresentationBuilder
     */
    private $genericBuilder;

    /**
     * @var array
     */
    private $formatMap = array('hal+json' => 'toJson');

    public function __construct(FormatBuilderInterface $formatBuilder, GenericRepresentationBuilder $genericBuilder)
    {
        $this->genericBuilder = $genericBuilder;
        $this->formatBuilder  = $formatBuilder;
    }

    public function serialize($object, $format, $view = null)
    {
        if (!$this->supports($format)) {
            throw new \Exception(get_class($this).' is not configured to support the format: '.$format);
        }
        $method = $this->formatMap[$format];

        return $this->$method($object, $view);
    }

    public function supports($format)
    {
        return array_key_exists($format, $this->formatMap);
    }

    /**
     * Handles serializing an object to json
     *
     * @param      $object
     * @param null $view
     * @return string
     */
    private function toJson($object, $view = null)
    {
        $representation = $this->genericBuilder->buildRepresentation($object, $view);

        return json_encode($this->formatBuilder->buildRepresentation($representation, $object, $view));
    }
}