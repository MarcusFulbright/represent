<?php

namespace Represent\Test\Builder;

use Represent\Builder\PropertyMetaDataBuilder;
use Represent\Enum\PropertyTypeEnum;
use Represent\Tests\RepresentTestCase;

class PropertyMetaDataBuilderTest extends RepresentTestCase
{
    public function testHandleTypeConversionToInt()
    {
        $type  = PropertyTypeEnum::INTEGER;
        $value = '1';

        $builder = new PropertyMetaDataBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleTypeConversion')->invoke($builder, $type, $value);

        $this->assertInternalType('int', $result);
        $this->assertEquals($result, 1);
    }

    public function testHandleTypeConversionToString()
    {
        $type  = PropertyTypeEnum::STRING;
        $value = 1;

        $builder = new PropertyMetaDataBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleTypeConversion')->invoke($builder, $type, $value);

        $this->assertInternalType('string', $result);
        $this->assertEquals($result, '1');
    }

    public function testHandleTypeConversionToBoolean()
    {
        $type  = PropertyTypeEnum::BOOLEAN;
        $value = 'true';

        $builder = new PropertyMetaDataBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleTypeConversion')->invoke($builder, $type, $value);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals($result, true);
    }   
}
