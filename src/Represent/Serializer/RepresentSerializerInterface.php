<?php

namespace Represent\Serializer;

interface RepresentSerializerInterface
{
    public function serialize($object, $format, $view = null);

    public function supports($format);
}