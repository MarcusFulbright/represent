<?php

namespace Reprsent\Tests\Enum\SuperClass;

use Represent\Enum\SuperClass\AbstractEnum;

class AbstractEnumTest extends \PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $this->assertEquals(
            array(
                'TEST1' => 'test1',
                'TEST2' => 'test2',
                'TEST3' => 'test3',
            ),
            TestEnum::toArray()
        );
    }
}

class TestEnum extends AbstractEnum
{
    const TEST1 = 'test1';
    const TEST2 = 'test2';
    const TEST3 = 'test3';
}