<?php

namespace Represent\Builder\Format;

interface FormatBuilderInterface
{
    public function buildRepresentation($representation, $object, $view = null);
}