<?php

namespace Represent\Tests\Builder;

use Represent\Builder\Format\HalFormatBuilder;
use Represent\Test\RepresentTestCase;

class HalBuilderTest extends RepresentTestCase
{
    public function testGetEmbedded()
    {
        $name           = 'name';
        $value          = array(1,2,3);
        $representation = \Mockery::mock('stdClass');
        $object         = \Mockery::mock('stdClass');
        $reflection     = $this->getBasicReflectionClassMock();
        $property       = $this->getBasicReflectionPropertyMock();
        $annot          = $this->getEmbeddedMock();
        $reader         = $this->getAnnotationReaderMock();

        $reflection->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\HalEmbedded')->andReturn($annot);
        $property->shouldReceive('getName')->andReturn($name);
        $property->shouldReceive('getValue')->with($object)->andReturn($value);

        $builder = new HalFormatBuilder($reader, $this->getLinkGeneratorMock());
        $result  = $this->getReflectedMethod($builder, 'getEmbedded')->invoke($builder, $representation, $object, $reflection);

        $this->assertEquals($value, $result->$name);
    }

    public function testGetEmbeddedEmpty()
    {
        $representation = \Mockery::mock('stdClass');
        $object         = \Mockery::mock('stdClass');
        $reflection     = $this->getBasicReflectionClassMock();
        $property       = $this->getBasicReflectionPropertyMock();
        $reader         = $this->getAnnotationReaderMock();

        $reflection->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\HalEmbedded')->andReturnNull();

        $builder = new HalFormatBuilder($reader, $this->getLinkGeneratorMock());
        $result  = $this->getReflectedMethod($builder, 'getEmbedded')->invoke($builder, $representation, $object, $reflection);

        $this->assertEquals(new \stdClass(), $result);
    }

    public function testGetLinks()
    {
        $group      = 'group';
        $name       = 'name';
        $uri        = 'www.example.com';
        $context    = $this->getClassContextMock();
        $reader     = $this->getAnnotationReaderMock();
        $reflection = $this->getBasicReflectionClassMock();
        $annot      = $this->getLinkCollectionMock();
        $link       = $this->getLinkMock();
        $generator  = $this->getLinkGeneratorMock();

        $annot->links = array($link);
        $context->group  = null;
        $link->group  = $group;
        $link->name   = $name;
        $generator->shouldReceive('generate')->with($link)->andReturn($uri);
        $reader->shouldReceive('getClassAnnotation')->with($reflection, '\Represent\Annotations\LinkCollection')->andReturn($annot);

        $builder = new HalFormatBuilder($reader, $generator);
        $result  = $this->getReflectedMethod($builder, 'getLinks')->invoke($builder, $reflection, $context);

        $this->assertEquals($uri, $result->$name);
    }

    public function testGetLinksEmpty()
    {
        $context    = $this->getClassContextMock();
        $reader     = $this->getAnnotationReaderMock();
        $reflection = $this->getBasicReflectionClassMock();

        $reader->shouldReceive('getClassAnnotation')->with($reflection, '\Represent\Annotations\LinkCollection')->andReturnNull();

        $builder = new HalFormatBuilder($reader, $this->getLinkGeneratorMock());
        $result  = $this->getReflectedMethod($builder, 'getLinks')->invoke($builder, $reflection, $context);

        $this->assertEquals(new \stdClass(), $result);
    }

    public function testParseLinks()
    {
        $name      = 'name';
        $group     = 'group';
        $uri       = 'www.example.com';
        $annot     = $this->getLinkCollectionMock();
        $context   = $this->getClassContextMock();
        $output    = \Mockery::mock('stdClass');
        $link      = $this->getLinkMock();
        $generator = $this->getLinkGeneratorMock();

        $annot->links = array($link);
        $context->group  = $group;
        $link->group  = $group;
        $link->name   = $name;
        $generator->shouldReceive('generate')->with($link)->andReturn($uri);


        $builder = new HalFormatBuilder($this->getAnnotationReaderMock(), $generator);
        $result  = $this->getReflectedMethod($builder, 'parseLinks')->invoke($builder, $annot, $context, $output);

        $this->assertEquals($result->$name, $uri);
    }

    public function testParseLinksRespectsGroups()
    {
        $name      = 'name';
        $group     = 'group';
        $uri       = 'www.example.com';
        $annot     = $this->getLinkCollectionMock();
        $context   = $this->getClassContextMock();
        $output    = \Mockery::mock('stdClass');
        $link      = $this->getLinkMock();
        $generator = $this->getLinkGeneratorMock();

        $annot->links = array($link);
        $context->group  = null;
        $link->group  = $group;
        $link->name   = $name;
        $generator->shouldReceive('generate')->with($link)->andReturn($uri);


        $builder = new HalFormatBuilder($this->getAnnotationReaderMock(), $generator);
        $result  = $this->getReflectedMethod($builder, 'parseLinks')->invoke($builder, $annot, $context, $output);

        $this->assertEquals($result, $output);
    }
}