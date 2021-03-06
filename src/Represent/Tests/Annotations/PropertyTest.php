<?php

namespace Represent\Tests\Annotations;

use Represent\Annotations\Property;
use Represent\Test\RepresentTestCase;

class PropertyTest extends RepresentTestCase
{
    public function testDoesNotAllowInvalidProperties()
    {
        $this->setExpectedException(
            'InvalidArgumentException', 'Property "wrong" does not exist'
        );

        new Property(array('wrong' => 'invalid'));
    }

    public function testMustHaveCorrectValueForType()
    {
        $this->setExpectedException(
            'InvalidArgumentException', 'type must be one of the following values: integer, string, boolean, datetime'
        );

        new Property(array('type' => 'invalid'));
    }
}