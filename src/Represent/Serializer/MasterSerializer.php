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
    private $configs = array();

    /**
     * Takes any number of arrays with the following format:
     * array('format' => $serializer);
     */
    public function __construct(array $formatMap)
    {
        foreach ($formatMap as $format => $serializer) {
            if (!$serializer instanceof RepresentSerializerInterface) { //need to make this interface
                throw new \Exception('Serializers must implement MySerializerInterface');
            }
            $this->configs[$format] = $serializer;
        }
    }

    public function serialize($object, $format, $view = null)
    {
        if (!array_key_exists($format, $this->configs)) {
            throw new \Exception($format.' is not configured');
        }

        return $this->configs[$format]->serialize($object, $format, $view);
    }
}
