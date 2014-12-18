<?php

namespace Represent\Tests\Handler;

use Represent\Enum\PropertyTypeEnum;
use Represent\Handler\PropertyHandler;
use Represent\Test\RepresentTestCase;

class PropertyHandlerTest extends RepresentTestCase
{
    private function getPropertyHandler($reader = null)
    {
        if ($reader == null) {
            $reader = $this->getAnnotationReaderMock();
        }

        return new PropertyHandler($reader);
    }

    public function testGetPropertyAnnotation()
    {
        $mock   = $this->getBasicReflectionPropertyMock();
        $annot  = $this->getPropertyMock();
        $reader = $this->getAnnotationReaderMock();
        $reader->shouldReceive('getPropertyAnnotation')->with($mock, '\Represent\Annotations\Property')->andReturn($annot);

        $handler = $this->getPropertyHandler($reader);
        $this->assertEquals($annot, $handler->getPropertyAnnotation($mock));
    }

    public function testGetPropertyAnnotaitonFalse()
    {
        $mock   = $this->getBasicReflectionPropertyMock();
        $reader = $this->getAnnotationReaderMock();
        $reader->shouldReceive('getPropertyAnnotation')->with($mock, '\Represent\Annotations\Property')->andReturn(null);
        $handler = $this->getPropertyHandler($reader);

        $this->assertFalse($handler->getPropertyAnnotation($mock));
    }

    public function testGetSerializedName()
    {
        $name   = 'nameFromAnnotation';
        $mock   = $this->getBasicReflectionPropertyMock();
        $reader = $this->getAnnotationReaderMock();
        $annot  = $this->getPropertyMock();

        $reader->shouldReceive('getPropertyAnnotation')->with($mock, '\Represent\Annotations\Property')->andReturn($annot);
        $annot->shouldReceive('getName')->andReturn($name);
        $mock->shouldReceive('getName')->never();

        $handler = $this->getPropertyHandler($reader);
        $this->assertEquals($name, $handler->getSerializedName($mock));
    }

    public function testGetSerializedNameWithAnnot()
    {
        $name  = 'nameFromAnnotation';
        $mock  = $this->getBasicReflectionPropertyMock();
        $annot = $this->getPropertyMock();

        $annot->shouldReceive('getName')->andReturn($name);
        $mock->shouldReceive('getName')->never();

        $handler = $this->getPropertyHandler();
        $this->assertEquals($name, $handler->getSerializedName($mock, $annot));
    }

    public function testGetSerializedNameNoAnnotation()
    {
        $name   = 'NameFromProperty';
        $mock   = $this->getBasicReflectionPropertyMock();
        $reader = $this->getAnnotationReaderMock();
        $annot  = $this->getPropertyMock();

        $reader->shouldReceive('getPropertyAnnotation')->with($mock, '\Represent\Annotations\Property')->andReturn(null);
        $annot->shouldReceive('getName')->never();
        $mock->shouldReceive('getName')->andReturn($name);

        $handler = $this->getPropertyHandler($reader);
        $this->assertEquals($name, $handler->getSerializedName($mock));
    }

    public function testPropertyTypeOverride()
    {
        $type = 'annotType';
        $mock  = $this->getBasicReflectionPropertyMock();
        $annot = $this->getPropertyMock();

        $annot->shouldReceive('getType')->andReturn($type);
        $handler = $this->getPropertyHandler();

        $this->assertEquals($type, $handler->propertyTypeOverride($annot, $mock));
    }

    public function testPropertyTypeOverrideReturnsNull()
    {
        $reader = $this->getAnnotationReaderMock();
        $reader->shouldReceive('getPropertyAnnotation')->andReturnNull();

        $handler = $this->getPropertyHandler($reader);
        $this->assertEquals(null, $handler->propertyTypeOverride(null, $this->getBasicReflectionPropertyMock()));
    }

    public function testHandleTypeConversionToString()
    {
        $handler = $this->getPropertyHandler();
        $result  = $handler->handleTypeConversion(PropertyTypeEnum::STRING, 2);

        $this->assertInternalType('string', $result);
        $this->assertEquals('2', $result);
    }

    public function testHandleTypeConversionBool()
    {
        $handler = $this->getPropertyHandler();
        $result  = $handler->handleTypeConversion(PropertyTypeEnum::BOOLEAN, 'true');

        $this->assertInternalType('bool', $result);
        $this->assertEquals(true, $result);
    }

    public function testHandleTypeConversionInteger()
    {
        $handler = $this->getPropertyHandler();
        $result  = $handler->handleTypeConversion(PropertyTypeEnum::INTEGER, '2');

        $this->assertInternalType('integer', $result);
        $this->assertEquals(2, $result);
    }
}