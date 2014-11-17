<?php

namespace Represent\Test\Builder;

use Represent\Builder\PropertyMetaDataBuilder;
use Represent\Enum\PropertyTypeEnum;
use Represent\Tests\RepresentTestCase;

class PropertyMetaDataBuilderTest extends RepresentTestCase
{
    public function testPropertyMetaFromReflection()
    {
        $value    = 'test';
        $property = $this->getBasicReflectionPropertyMock();
        $original = \Mockery::mock('\stdClass');
        $reader   = $this->getAnnotationReaderMock();

        $property->shouldReceive('getValue')->with($original)->andReturn($value);
        $property->shouldReceive('setAccessible')->once()->with(true);
        $reader->shouldReceive('getPropertyAnnotations')->with($property)->andReturn(array());

        $builder = new PropertyMetaDataBuilder($reader);
        $result  = $builder->propertyMetaFromReflection($property, $original);

        $this->assertEquals($value, $result->value);
        $this->assertInternalType('string', $result->value);
    }


    public function testParseAnnotations()
    {
        $meta     = $this->getPropertyMetaDataMock();
        $property = $this->getBasicReflectionPropertyMock();
        $reader   = $this->getAnnotationReaderMock();
        $annot    = $this->getPropertyMock();
        $name     = 'test-name';

        $reader->shouldReceive('getPropertyAnnotations')->with($property)->andReturn(array($annot));
        $annot->shouldReceive('getName')->andReturn($name);
        $annot->shouldReceive('getType')->andReturnNull();

        $builder = new PropertyMetaDataBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'parseAnnotations')->invoke($builder, $meta, $property, $meta);

        $this->assertEquals($name, $result->name);
    }

    public function testHandleProperty()
    {
        $name     = 'test-name';
        $value    = 'test-value';
        $type     = PropertyTypeEnum::STRING;
        $annot    = $this->getPropertyMock();
        $property = $this->getBasicReflectionPropertyMock();
        $meta     = $this->getPropertyMetaDataMock();
        $original = \Mockery::mock('\stdClass');

        $annot->shouldReceive('getName')->andReturn($name);
        $annot->shouldReceive('getType')->andReturn($type);
        $property->shouldReceive('getValue')->with($original)->andReturn($value);

        $builder = new PropertyMetaDataBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleRepresentProperty')->invoke($builder, $property, $annot, $meta, $original);

        $this->assertEquals($name, $result->name);
        $this->assertEquals($value, $result->value);
    }

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

    public function testHandleTypeConversionToDate()
    {
        $type  = PropertyTypeEnum::DATETIME;
        $value = '11/17/89';

        $builder = new PropertyMetaDataBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleTypeConversion')->invoke($builder, $type, $value);

        $this->assertInstanceOf('DateTime', $result);
        $this->assertEquals('627260400', $result->getTimeStamp());
    }
}
