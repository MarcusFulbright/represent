<?php

namespace Represent\Builder\Format;

use Represent\MetaData\ClassMetaData;

interface FormatBuilderInterface
{
    public function buildRepresentation($representation, $object, ClassMetaData $meta);
}