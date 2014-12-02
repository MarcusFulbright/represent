<?php

namespace Represent\Serializer;

use Represent\Builder\GenericRepresentationBuilder;

class GenericSerializer implements RepresentSerializerInterface
{
    /**
     * @var \Represent\Builder\GenericRepresentationBuilder
     */
    private $genericBuilder;

    /**
     * @var array
     */
    private $formatMap = array('json' => 'toJson');

    public function __construct(GenericRepresentationBuilder $genericBuilder)
    {
        $this->genericBuilder = $genericBuilder;
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
        return json_encode($this->genericBuilder->buildRepresentation($object, $view));
    }
}
