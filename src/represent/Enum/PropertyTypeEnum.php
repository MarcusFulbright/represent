<?php

namespace Represent\Enum;

use Represent\Enum\SuperClass\AbstractEnum;

class PropertyTypeEnum extends AbstractEnum
{
    const INTEGER  = 'integer';
    const STRING   = 'string';
    const BOOLEAN  = 'boolean';
    const DATETIME = 'datetime';
}