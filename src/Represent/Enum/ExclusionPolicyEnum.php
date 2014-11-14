<?php

namespace Represent\Enum;

use Represent\Enum\SuperClass\AbstractEnum;

class ExclusionPolicyEnum extends AbstractEnum
{
    CONST BLACKLIST = 'blackList';
    CONST WHITELIST = 'whiteList';
}