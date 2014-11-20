<?php

namespace Represent\Serializer;

interface JsonSerializerInterface
{
    public function toJson($object, $view = null);
}