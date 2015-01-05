<?php

namespace Represent\Tests\Builder;

use Represent\Builder\Format\HalFormatBuilder;
use Represent\Test\RepresentTestCase;

class HalBuilderTest extends RepresentTestCase
{
    private function getHalFormatBuilder($propBuilder = null, $classBuilder = null, $reader = null, $generator = null)
    {
        $propBuilder  = $propBuilder  === null ? $this->getPropertyBuilderMock() : $propBuilder;
        $classBuilder = $classBuilder === null ? $this->getClassBuilderMock()    : $classBuilder;
        $reader       = $reader       === null ? $this->getAnnotationReaderMock(): $reader;
        $generator    = $generator    === null ? $this->getLinkGeneratorMock()   : $generator;
        $config       = array('reader' => $reader, 'linkGenerator' => $generator);

        return new HalFormatBuilder($propBuilder, $classBuilder, $config);
    }

    public function testGetEmbedded()
    {
        $name           = 'name';
        $value          = array(1,2,3);
        $representation = \Mockery::mock('stdClass');
        $reflection     = $this->getBasicReflectionClassMock();
        $property       = $this->getBasicReflectionPropertyMock();
        $annot          = $this->getEmbeddedMock();
        $reader         = $this->getAnnotationReaderMock();

        $reflection->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Embedded')->andReturn($annot);
        $property->shouldReceive('getName')->andReturn($name);
        $property->shouldReceive('setAccessible')->with(true);
        $representation->$name  = $value;

        $builder = $this->getHalFormatBuilder(null, null, $reader);
        $result  = $this->getReflectedMethod($builder, 'getEmbedded')->invoke($builder, $representation, $reflection);

        $this->assertEquals($value, $result->$name);
    }

    public function testGetEmbeddedEmpty()
    {
        $representation = \Mockery::mock('stdClass');
        $reflection     = $this->getBasicReflectionClassMock();
        $property       = $this->getBasicReflectionPropertyMock();
        $reader         = $this->getAnnotationReaderMock();

        $reflection->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Embedded')->andReturnNull();
        $property->shouldReceive('setAccessible')->with(true);

        $builder = $this->getHalFormatBuilder(null, null, $reader);
        $result  = $this->getReflectedMethod($builder, 'getEmbedded')->invoke($builder, $representation, $reflection);

        $this->assertEquals(new \stdClass(), $result);
    }

    public function testGetLinks()
    {
        $name       = 'name';
        $uri        = 'www.example.com';
        $reader     = $this->getAnnotationReaderMock();
        $annot      = $this->getLinkCollectionMock();
        $link       = $this->getLinkMock();
        $generator  = $this->getLinkGeneratorMock();
        $object     = new \stdClass();

        $annot->links = array($link);
        $link->name   = $name;
        $generator->shouldReceive('generate')->andReturn($uri);
        $generator->shouldReceive('parseName')->andReturn($name);
        $reader->shouldReceive('getClassAnnotation')->andReturn($annot);

        $builder = $this->getHalFormatBuilder(null, null, $reader, $generator);
        $result  = $this->getReflectedMethod($builder, 'getLinks')->invoke($builder, new \stdClass(), null);

        $this->assertEquals($uri, $result->$name);
    }

    public function testGetLinksEmpty()
    {
        $context = $this->getClassContextMock();
        $reader  = $this->getAnnotationReaderMock();
        $object  = new \stdClass();

        $reader->shouldReceive('getClassAnnotation')->andReturnNull();

        $builder = $this->getHalFormatBuilder(null, null, $reader);
        $result  = $this->getReflectedMethod($builder, 'getLinks')->invoke($builder, new \stdClass(), $context);

        $this->assertEquals(new \stdClass(), $result);
    }

    public function testParseLinks()
    {
        $name      = 'name';
        $view      = 'view';
        $uri       = 'www.example.com';
        $annot     = $this->getLinkCollectionMock();
        $output    = \Mockery::mock('stdClass');
        $link      = $this->getLinkMock();
        $generator = $this->getLinkGeneratorMock();
        $object    = new \stdClass();

        $annot->links = array($link);
        $link->views  = array($view);
        $link->name   = $name;
        $generator->shouldReceive('generate')->with($link, $object)->andReturn($uri);
        $generator->shouldReceive('parseName')->with($link, $object)->andReturn($name);


        $builder = $this->getHalFormatBuilder(null, null, null, $generator);
        $result  = $this->getReflectedMethod($builder, 'parseLinks')->invoke($builder, $annot, $view, $output, $object);

        $this->assertEquals($result->$name, $uri);
    }

    public function testParseLinksRespectsViews()
    {
        $name      = 'name';
        $view      = 'view';
        $uri       = 'www.example.com';
        $annot     = $this->getLinkCollectionMock();
        $output    = \Mockery::mock('stdClass');
        $link      = $this->getLinkMock();
        $generator = $this->getLinkGeneratorMock();
        $object    = new \stdClass();
        $generator->shouldReceive('parseName')->with($link, $object)->andReturn($name);

        $annot->links  = array($link);
        $link->views   = array($view);
        $link->name    = $name;
        $generator->shouldReceive('generate')->with($link, $object)->andReturn($uri);


        $builder = $this->getHalFormatBuilder(null, null, null, $generator);
        $result  = $this->getReflectedMethod($builder, 'parseLinks')->invoke($builder, $annot, $view, $output, $object);

        $this->assertEquals($result, $output);
    }
}