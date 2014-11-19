<?php

namespace Represent\Tests\Annotations;

use Represent\Annotations\ExclusionPolicy;
use Represent\Test\RepresentTestCase;

class ExclusionPolicyTest extends RepresentTestCase
{
    public function testOnlyAllowsForPolicy()
    {
        $this->setExpectedException(
            'InvalidArgumentException', 'Property "wrong" does not exist'
        );

        new ExclusionPolicy(array('wrong' => 'invalid'));
    }

    public function testMustHaveCorrectValueForPolicy()
    {
        $this->setExpectedException(
            'InvalidArgumentException', 'type must be one of the following values: blackList, whiteList'
        );

        new ExclusionPolicy(array('policy' => 'invalid'));
    }
}