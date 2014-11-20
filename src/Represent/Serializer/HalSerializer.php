<?php

namespace Represent\Serializer;

use Represent\Builder\Format\FormatBuilderInterface;
use Represent\Builder\GenericRepresentationBuilder;

class HalSerializer Implements JsonSerializerInterface
{
    /**
     * @var \Represent\Builder\Format\FormatBuilderInterface
     */
    private $format;

    /**
     * @var \Represent\Builder\GenericRepresentationBuilder
     */
    private $generic;

    public function __construct(FormatBuilderInterface $formatBuilder, GenericRepresentationBuilder $genericBuilder)
    {
        $this->generic = $genericBuilder;
        $this->format  = $formatBuilder;
    }

    /**
     * Handles serializing an object to json
     *
     * @param      $object
     * @param null $group
     * @return string
     */
    public function toJson($object, $group = null)
    {
        $representation = $this->generic->buildRepresentation($object, $group);

        return json_encode($this->format->buildRepresentation($representation, $object, $group));
    }
}