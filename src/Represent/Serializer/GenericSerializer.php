<?php

namespace Represent\Serializer;

use Represent\Builder\GenericRepresentationBuilder;

class GenericSerializer implements JsonSerializerInterface
{
    /**
     * @var \Represent\Builder\GenericRepresentationBuilder
     */
    private $builder;

    public function __construct(GenericRepresentationBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Handles serializing an object to json
     *
     * @param      $object
     * @param null $view
     * @return string
     */
    public function toJson($object, $view = null)
    {
        return json_encode($this->builder->buildRepresentation($object, $view));
    }
}