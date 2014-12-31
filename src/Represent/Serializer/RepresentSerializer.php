<?php

namespace Represent\Serializer;

use Represent\Builder\BuilderInterface;

class RepresentSerializer implements RepresentSerializerInterface
{
    /**
     * @var \Represent\Builder\GenericRepresentationBuilder
     */
    private $builder;

    /**
     * @var array
     */
    private $formatMap;

    public function __construct(BuilderInterface $builder, array $formatMap = array('json' => 'toJson'))
    {
        $this->builder   = $builder;
        $this->formatMap = $formatMap;
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
        return json_encode($this->builder->buildRepresentation($object, $view));
    }
}
