<?php

namespace Represent\Serializer;

class MasterSerializer
{
    /**
     * @var HalSerializer
     */
    private $hal;

    /**
     * @var GenericSerializer
     */
    private $generic;

    public function __construct(GenericSerializer $generic, HalSerializer $hal)
    {
        $this->generic = $generic;
        $this->hal  = $hal;
    }

    /**
     * Can handle parsing through all available formats and serializing accordingly
     *
     * @param $object
     * @param $format
     * @return string
     */
    public function serialize($object, $format)
    {
        switch ($format):
            case 'application/json':
                $output = $this->generic->toJson($object);
                break;
            case 'application/hal+json':
                $output = $this->hal->toJson($object);
         endswitch;

        return $output;
    }
}