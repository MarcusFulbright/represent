<?php

namespace Represent\Builder\Format;

use Represent\Context\ClassContext;

interface FormatBuilderInterface
{
    public function buildRepresentation($representation, $object, ClassContext $context);
}