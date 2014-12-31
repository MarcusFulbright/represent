<?php

namespace Represent\Builder;

interface BuilderInterface
{
    public function buildRepresentation($object, $view = null);
}