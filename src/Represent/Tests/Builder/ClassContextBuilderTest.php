<?php

namespace Represent\Tests\Builder;

use Represent\Builder\ClassContextBuilder;
use Represent\Enum\ExclusionPolicyEnum;
use Represent\Test\RepresentTestCase;

class ClassContextBuilderTest extends RepresentTestCase
{
    public function testBuildClassContextWithGroup()
    {
        $class     = $this->getBasicReflectionClassMock();
        $reader    = $this->getAnnotationReaderMock();
        $property  = $this->getBasicReflectionPropertyMock();
        $group     = $this->getGroupMock();
        $groupName = 'test';
        $context   = $this->getClassContextMock();

        $context->properties = array();
        $group->name         = array($groupName);
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturnNull();
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturnNull();
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Group')->andReturn($group);

        $builder = new ClassContextBuilder($reader);
        $result  = $builder->buildClassContext($class, $groupName);

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
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Group')->andReturnNull();

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

    public function testHandleGroupTrue()
    {
        $context   = $this->getClassContextMock();
        $reader    = $this->getAnnotationReaderMock();
        $property  = $this->getBasicReflectionPropertyMock();
        $group     = $this->getGroupMock();
        $groupName = 'test';

        $context->properties = array($property);
        $context->group      = $groupName;
        $group->name      = array($groupName);
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Group')->andReturn($group);

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleGroup')->invoke($builder, $context);

        $this->assertEquals(array($property), $result->properties);
    }

    public function testHandleGroupFalse()
    {
        $context  = $this->getClassContextMock();
        $reader   = $this->getAnnotationReaderMock();
        $property = $this->getBasicReflectionPropertyMock();
        $group    = $this->getGroupMock();

        $context->properties = array($property);
        $context->group      = 'wrong';
        $group->name      = array('test');
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Group')->andReturn($group);

        $builder = new ClassContextBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleGroup')->invoke($builder, $context);

        $this->assertTrue(empty($result->properties));
    }
}