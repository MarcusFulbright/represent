<?php

namespace Represent\Serializer;

/**
 * Handles supporting serialization of multiple formats
 */
class MasterSerializer implements RepresentSerializerInterface
{
    /**
     * @var array
     */
    private $formatMap = array();

    /**
     * Takes any number of arrays with the following format:
     * array('format' => $serializer);
     */
    public function __construct(array $formatMap)
    {
        foreach ($formatMap as $format => $serializer) {
            if (!$serializer instanceof RepresentSerializerInterface) {
                throw new \Exception('Serializers must implement RepresentSerializerInterface');
            }
            $this->formatMap[$format] = $serializer;
        }
    }

    public function serialize($object, $format, $view = null)
    {
        if (!array_key_exists($format, $this->formatMap)) {
            throw new \Exception($format.' is not configured');
        }

        return $this->formatMap[$format]->serialize($object, $format, $view);
    }

    public function supports($format)
    {
        return array_key_exists($format, $this->formatMap);
    }
}
