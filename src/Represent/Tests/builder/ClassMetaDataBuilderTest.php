<?php

namespace Represent\Tests\Builder;

use Represent\Builder\ClassMetaDataBuilder;
use Represent\Enum\ExclusionPolicyEnum;
use Represent\Tests\RepresentTestCase;

class ClassMetaDataBuilderTest extends RepresentTestCase
{
    public function testBuildClassMetaDataWithGroup()
    {
        $class     = $this->getBasicReflectionClassMock();
        $reader    = $this->getAnnotationReaderMock();
        $property  = $this->getBasicReflectionPropertyMock();
        $group     = $this->getGroupMock();
        $groupName = 'test';
        $meta      = $this->getClassMetaDataMock();

        $meta->properties = array();
        $group->name      = array($groupName);
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturnNull();
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturnNull();
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Group')->andReturn($group);

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $builder->buildClassMetaData($class, $groupName);

        $this->assertEquals(array($property), $result->properties);
    }


    public function testBuildClassMetaData()
    {
        $class    = $this->getBasicReflectionClassMock();
        $meta     = $this->getClassMetaDataMock();
        $reader   = $this->getAnnotationReaderMock();
        $property = $this->getBasicReflectionPropertyMock();

        $meta->properties = array();
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturnNull();
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturnNull();

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $builder->buildClassMetaData($class);

        $this->assertInstanceOf('Represent\MetaData\ClassMetaData', $result);
        $this->assertEquals(array($property), $result->properties);
        $this->assertEquals(ExclusionPolicyEnum::WHITELIST, $result->policy);
    }


    public function testHandleExclusionPolicyBlackList()
    {
        $class    = $this->getBasicReflectionClassMock();
        $meta     = $this->getClassMetaDataMock();
        $reader   = $this->getAnnotationReaderMock();
        $policy   = $this->getExclusionPolicyMock();
        $property = $this->getBasicReflectionPropertyMock();

        $meta->properties = array();
        $policy->shouldReceive('getPolicy')->andReturn(ExclusionPolicyEnum::BLACKLIST);
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturn($policy);
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Show')->andReturn($this->getShowMock());

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleExclusionPolicy')->invoke($builder, $class, $meta);

        $this->assertInstanceOf('Represent\MetaData\ClassMetaData', $result);
        $this->assertEquals(array($property), $result->properties);
        $this->assertEquals(ExclusionPolicyEnum::BLACKLIST, $result->policy);
    }


    public function testHandleExclusionPolicyNull()
    {
        $class    = $this->getBasicReflectionClassMock();
        $meta     = $this->getClassMetaDataMock();
        $reader   = $this->getAnnotationReaderMock();
        $property = $this->getBasicReflectionPropertyMock();

        $meta->properties = array();
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturnNull();
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturnNull();

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleExclusionPolicy')->invoke($builder, $class, $meta);

        $this->assertInstanceOf('Represent\MetaData\ClassMetaData', $result);
        $this->assertEquals(array($property), $result->properties);
        $this->assertEquals(ExclusionPolicyEnum::WHITELIST, $result->policy);
    }

    public function testHandleExclusionPolicyWhiteList()
    {
        $class    = $this->getBasicReflectionClassMock();
        $meta     = $this->getClassMetaDataMock();
        $reader   = $this->getAnnotationReaderMock();
        $policy   = $this->getExclusionPolicyMock();
        $property = $this->getBasicReflectionPropertyMock();

        $meta->properties = array();
        $policy->shouldReceive('getPolicy')->andReturn(ExclusionPolicyEnum::WHITELIST);
        $reader->shouldReceive('getClassAnnotation')->with($class, '\Represent\Annotations\ExclusionPolicy')->andReturn($policy);
        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturn(null);

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleExclusionPolicy')->invoke($builder, $class, $meta);

        $this->assertInstanceOf('Represent\MetaData\ClassMetaData', $result);
        $this->assertEquals(array($property), $result->properties);
        $this->assertEquals(ExclusionPolicyEnum::WHITELIST, $result->policy);
    }

    public function testGeneratePropertiesForBlackListNoShow()
    {
        $class    = $this->getBasicReflectionClassMock();
        $meta     = $this->getClassMetaDataMock();
        $property = $this->getBasicReflectionPropertyMock();
        $reader   = $this->getAnnotationReaderMock();

        $class->shouldReceive('getProperties')->andReturn(array($property));
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Show')->andReturn(null);
        $meta->properties = array();

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'generatePropertiesForBlackList')->invoke($builder, $class, $meta);

        $this->asserttrue(empty($result->properties));
    }

    public function testGeneratePropertiesForBlackListWithShow()
   {
       $class    = $this->getBasicReflectionClassMock();
       $meta     = $this->getClassMetaDataMock();
       $property = $this->getBasicReflectionPropertyMock();
       $reader   = $this->getAnnotationReaderMock();

       $class->shouldReceive('getProperties')->andReturn(array($property));
       $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Show')->andReturn($this->getShowMock());
       $meta->properties = array();

       $builder = new ClassMetaDataBuilder($reader);
       $result  = $this->getReflectedMethod($builder, 'generatePropertiesForBlackList')->invoke($builder, $class, $meta);

       $this->assertEquals(array($property), $result->properties);
   }

    public function testGeneratePropertiesForWhiteListNoHide()
    {
        $property = $this->getBasicReflectionPropertyMock();
        $class    = $this->getBasicReflectionClassMock();
        $meta     = $this->getClassMetaDataMock();
        $reader   = $this->getAnnotationReaderMock();

        $class->shouldReceive('getProperties')->andReturn(array($property));
        $meta->properties = array();
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturn(null);

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $this->getReflectedMethod(new ClassMetaDataBuilder($reader), 'generatePropertiesForWhiteList')->invoke($builder, $class, $meta);

        $this->assertEquals(array($property), $result->properties);
    }


    public function testGeneratePropertiesForWhiteListWithHide()
    {
        $property = $this->getBasicReflectionPropertyMock();
        $class    = $this->getBasicReflectionClassMock();
        $meta     = $this->getClassMetaDataMock();
        $reader   = $this->getAnnotationReaderMock();

        $class->shouldReceive('getProperties')->andReturn(array($property));
        $meta->properties = array();
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Hide')->andReturn($this->getHideMock());

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $this->getReflectedMethod(new ClassMetaDataBuilder($reader), 'generatePropertiesForWhiteList')->invoke($builder, $class, $meta);

        $this->assertTrue(empty($result->properties));
    }

    public function testHandleGroupTrue()
    {
        $meta      = $this->getClassMetaDataMock();
        $reader    = $this->getAnnotationReaderMock();
        $property  = $this->getBasicReflectionPropertyMock();
        $group     = $this->getGroupMock();
        $groupName = 'test';

        $meta->properties = array($property);
        $meta->group      = $groupName;
        $group->name      = array($groupName);
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Group')->andReturn($group);

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleGroup')->invoke($builder, $meta);

        $this->assertEquals(array($property), $result->properties);
    }

    public function testHandleGroupFalse()
    {
        $meta      = $this->getClassMetaDataMock();
        $reader    = $this->getAnnotationReaderMock();
        $property  = $this->getBasicReflectionPropertyMock();
        $group     = $this->getGroupMock();

        $meta->properties = array($property);
        $meta->group      = 'wrong';
        $group->name      = array('test');
        $reader->shouldReceive('getPropertyAnnotation')->with($property, '\Represent\Annotations\Group')->andReturn($group);

        $builder = new ClassMetaDataBuilder($reader);
        $result  = $this->getReflectedMethod($builder, 'handleGroup')->invoke($builder, $meta);

        $this->assertTrue(empty($result->properties));
    }
}