<?php

namespace Represent\Tests\Builder;

use Represent\Builder\ClassContextBuilder;
use Represent\Enum\ExclusionPolicyEnum;
use Represent\Test\RepresentTestCase;

class ClassContextBuilderTest extends RepresentTestCase
{
    public function testBuildClassContextWithView()
    {
        $class     = $this->getBasicReflectionClassMock();
        $reader    = $this->getAnnotationReaderMock();
        $property  = $this->getBasicReflectionPropertyMock();
        $view      = $this->getViewMock();
        $viewName  = 'test';
        $context   = $this->getClassContextMock();

        $context->properties = array();
        $view->name          = array($viewName);
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturnNull();
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturnNull();
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\View')->andReturn($view);

        $builder = new ClassContextBuilder($reader);
        $result  = $builder->buildClassContext($class, $viewName);

        $this->assertEquals(array($property), $result->properties);
    }


    public function testBuildClassContext()
    {
        $class    = $this->getBasicReflectionClassMock();
        $context  = $this->getClassContextMock();
        $reader   = $this->getAnnotationReaderMock();
        $property = $this->getBasicReflectionPropertyMock();

        $context->properties = array();
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturnNull();
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturnNull();
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\View')->andReturnNull();

        $builder = new ClassContextBuilder($reader);
        $result  = $builder->buildClassContext($class);

        $this->assertInstanceOf('Represent\Context\ClassContext', $result);
        $this->assertEquals(array($property), $result->properties);
        $this->assertEquals(ExclusionPolicyEnum::WHITELIST, $result->policy);
    }


    public function testHandleExclusionPolicyBlackList()
    {
        $class    = $this->getBasicReflectionClassMock();
        $context  = $this->getClassContextMock();
        $reader   = $this->getAnnotationReaderMock();
        $policy   = $this->getExclusionPolicyMock();
        $property = $this->getBasicReflectionPropertyMock();

        $context->properties = array();
        $policy->shouldReceive('getPolicy')->andReturn(ExclusionPolicyEnum::BLACKLIST);
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturn($policy);
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Show')->andReturn($this->getShowMock());

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleExclusionPolicy')->invoke($builder, $class, $context);

        $this->assertInstanceOf('Represent\Context\ClassContext', $result);
        $this->assertEquals(array($property), $result->properties);
        $this->assertEquals(ExclusionPolicyEnum::BLACKLIST, $result->policy);
    }


    public function testHandleExclusionPolicyNull()
    {
        $class    = $this->getBasicReflectionClassMock();
        $context  = $this->getClassContextMock();
        $reader   = $this->getAnnotationReaderMock();
        $property = $this->getBasicReflectionPropertyMock();

        $context->properties = array();
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturnNull();
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturnNull();

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleExclusionPolicy')->invoke($builder, $class, $context);

        $this->assertInstanceOf('Represent\Context\ClassContext', $result);
        $this->assertEquals(array($property), $result->properties);
        $this->assertEquals(ExclusionPolicyEnum::WHITELIST, $result->policy);
    }

    public function testHandleExclusionPolicyWhiteList()
    {
        $class    = $this->getBasicReflectionClassMock();
        $context  = $this->getClassContextMock();
        $reader   = $this->getAnnotationReaderMock();
        $policy   = $this->getExclusionPolicyMock();
        $property = $this->getBasicReflectionPropertyMock();

        $context->properties = array();
        $policy->shouldReceive('getPolicy')->andReturn(ExclusionPolicyEnum::WHITELIST);
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturn($policy);
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturn(null);

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleExclusionPolicy')->invoke($builder, $class, $context);

        $this->assertInstanceOf('Represent\Context\ClassContext', $result);
        $this->assertEquals(array($property), $result->properties);
        $this->assertEquals(ExclusionPolicyEnum::WHITELIST, $result->policy);
    }

    public function testGeneratePropertiesForBlackListNoShow()
    {
        $class    = $this->getBasicReflectionClassMock();
        $context  = $this->getClassContextMock();
        $property = $this->getBasicReflectionPropertyMock();
        $reader   = $this->getAnnotationReaderMock();

        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Show')->andReturn(null);
        $context->properties = array();

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'generatePropertiesForBlackList')->invoke($builder, $class, $context);

        $this->assertTrue(empty($result->properties));
    }

    public function testGeneratePropertiesForBlackListWithShow()
   {
       $class    = $this->getBasicReflectionClassMock();
       $context  = $this->getClassContextMock();
       $property = $this->getBasicReflectionPropertyMock();
       $reader   = $this->getAnnotationReaderMock();

       $class->shouldReceive('getProperties')->andReturn(array($property));
       $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Show')->andReturn($this->getShowMock());
       $context->properties = array();

       $builder = new ClassContextBuilder($reader);
       $result  = $this->getReflectedMethod($builder, 'generatePropertiesForBlackList')->invoke($builder, $class, $context);

       $this->assertEquals(array($property), $result->properties);
   }

    public function testGeneratePropertiesForWhiteListNoHide()
    {
        $property = $this->getBasicReflectionPropertyMock();
        $class    = $this->getBasicReflectionClassMock();
        $context  = $this->getClassContextMock();
        $reader   = $this->getAnnotationReaderMock();

        $class->shouldReceive('getProperties')->andReturn(array($property));
        $context->properties = array();
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturn(null);

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod(new ClassContextBuilder($reader), 'generatePropertiesForWhiteList')->invoke($builder, $class, $context);

        $this->assertEquals(array($property), $result->properties);
    }


    public function testGeneratePropertiesForWhiteListWithHide()
    {
        $property = $this->getBasicReflectionPropertyMock();
        $class    = $this->getBasicReflectionClassMock();
        $context  = $this->getClassContextMock();
        $reader   = $this->getAnnotationReaderMock();

        $class->shouldReceive('getProperties')->andReturn(array($property));
        $context->properties = array();
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturn($this->getHideMock());

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'generatePropertiesForWhiteList')->invoke($builder, $class, $context);

        $this->assertTrue(empty($result->properties));
    }

    public function testHandleViewTrue()
    {
        $context   = $this->getClassContextMock();
        $reader    = $this->getAnnotationReaderMock();
        $property  = $this->getBasicReflectionPropertyMock();
        $view      = $this->getViewMock();
        $viewName = 'test';

        $context->properties = array($property);
        $context->view       = $viewName;
        $view->name          = array($viewName);
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\View')->andReturn($view);

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleView')->invoke($builder, $context);

        $this->assertEquals(array($property), $result->properties);
    }

    public function testHandleViewFalse()
    {
        $context  = $this->getClassContextMock();
        $reader   = $this->getAnnotationReaderMock();
        $property = $this->getBasicReflectionPropertyMock();
        $view    = $this->getViewMock();

        $context->properties = array($property);
        $context->view       = 'wrong';
        $view->name          = array('test');
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\View')->andReturn($view);

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleView')->invoke($builder, $context);

        $this->assertTrue(empty($result->properties));
    }
}