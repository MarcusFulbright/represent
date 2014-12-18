<?php

namespace Represent\Test\Builder;

use Represent\Builder\PropertyContextBuilder;
use Represent\Enum\PropertyTypeEnum;
use Represent\Test\RepresentTestCase;

class PropertyContextBuilderTest extends RepresentTestCase
{
    private function getPropertyContextBuilder($handler = null)
    {
        if ($handler === null) {
            $handler = $this->getPropertyHandlerMock();
        }

        return new PropertyContextBuilder($handler);
    }

    public function testHandleRepresentProperty()
    {
        $name       = 'annotPropName';
        $type       = 'string';
        $value      = 'value';
        $refleciton = $this->getBasicReflectionPropertyMock();
        $annot      = $this->getPropertyMock();
        $context    = $this->getPropertyContextMock();
        $original   = \Mockery::mock('\stdClass');
        $handler    = $this->getPropertyHandlerMock();

        $annot->shouldReceive('getName')->andReturn($name);
        $annot->shouldReceive('getType')->andReturn($type);
        $refleciton->shouldReceive('getValue')->with($original)->andReturn($value);
        $handler->shouldReceive('handleTypeConversion')->with($type, $value)->andReturn($value);

        $builder = $this->getPropertyContextBuilder($handler);
        $result = $this->getReflectedMethod($builder, 'handleRepresentProperty')->invoke($builder, $refleciton, $annot, $context, $original);

        $this->assertEquals(get_class($context), get_class($result));
        $this->assertEquals($name, $result->name);
        $this->assertEquals($value, $result->value);
    }
}
