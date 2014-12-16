<?php

namespace Represent\Instantiator;

Interface InstantiatorInterface
{
    public function instantiate(\stdClass $data, $class);

    public function supports(\stdClass $data, $class);

}