<?php

namespace Represent\Test\Builder;

use Represent\Builder\PropertyContextBuilder;
use Represent\Enum\PropertyTypeEnum;
use Represent\Test\RepresentTestCase;

class PropertyContextBuilderTest extends RepresentTestCase
{
    public function testPropertyContextFromReflection()
    {
        $value    = 'test';
        $property = $this->getBasicReflectionPropertyMock();
        $original = \Mockery::mock('\stdClass');
        $reader   = $this->getAnnotationReaderMock();

        $property->shouldReceive('getValue')->with($original)->andReturn($value);
        $property->shouldReceive('setAccessible')->once()->with(true);
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Property')->andReturnNull();

        $builder = new PropertyContextBuilder($reader);
        $result  = $builder->propertyContextFromReflection($property, $original);

        $this->assertEquals($value, $result->value);
        $this->assertInternalType('string', $result->value);
    }


    public function testParseAnnotations()
    {
        $context  = $this->getPropertyContextMock();
        $property = $this->getBasicReflectionPropertyMock();
        $reader   = $this->getAnnotationReaderMock();
        $annot    = $this->getPropertyMock();
        $name     = 'test-name';

        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Property')->andReturn($annot);
        $annot->shouldReceive('getName')->andReturn($name);
        $annot->shouldReceive('getType')->andReturnNull();

        $builder = new PropertyContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'parseAnnotations')->invoke($builder, $context, $property, $context);

        $this->assertEquals($name, $result->name);
    }

    public function testHandleProperty()
    {
        $name     = 'test-name';
        $value    = 'test-value';
        $type     = PropertyTypeEnum::STRING;
        $annot    = $this->getPropertyMock();
        $property = $this->getBasicReflectionPropertyMock();
        $context  = $this->getPropertyContextMock();
        $original = \Mockery::mock('\stdClass');

        $annot->shouldReceive('getName')->andReturn($name);
        $annot->shouldReceive('getType')->andReturn($type);
        $property->shouldReceive('getValue')->with($original)->andReturn($value);

        $builder = new PropertyContextBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleRepresentProperty')->invoke($builder, $property, $annot, $context, $original);

        $this->assertEquals($name, $result->name);
        $this->assertEquals($value, $result->value);
    }

    public function testHandleTypeConversionToInt()
    {
        $type  = PropertyTypeEnum::INTEGER;
        $value = '1';

        $builder = new PropertyContextBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleTypeConversion')->invoke($builder, $type, $value);

        $this->assertInternalType('int', $result);
        $this->assertEquals($result, 1);
    }

    public function testHandleTypeConversionToString()
    {
        $type  = PropertyTypeEnum::STRING;
        $value = 1;

        $builder = new PropertyContextBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleTypeConversion')->invoke($builder, $type, $value);

        $this->assertInternalType('string', $result);
        $this->assertEquals($result, '1');
    }

    public function testHandleTypeConversionToBoolean()
    {
        $type  = PropertyTypeEnum::BOOLEAN;
        $value = 'true';

        $builder = new PropertyContextBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleTypeConversion')->invoke($builder, $type, $value);

        $this->assertInternalType('boolean', $result);
        $this->assertEquals($result, true);
    }

    public function testHandleTypeConversionToDate()
    {
        $type  = PropertyTypeEnum::DATETIME;
        $value = '11/17/89';

        $builder = new PropertyContextBuilder($this->getAnnotationReaderMock());
        $result  = $this->getReflectedMethod($builder, 'handleTypeConversion')->invoke($builder, $type, $value);

        $this->assertInstanceOf('DateTime', $result);
        $this->assertEquals('627260400', $result->getTimeStamp());
    }
}
