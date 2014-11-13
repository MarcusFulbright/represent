<?php

namespace Represent\Enum;

use Represent\Enum\SuperClass\AbstractEnum;

/**
 * Contains values that can be used for Annotations\Property->Type
 */
class PropertyTypeEnum extends AbstractEnum
{
    const INTEGER  = 'integer';
    const STRING   = 'string';
    const BOOLEAN  = 'boolean';
    const DATETIME = 'datetime';
}