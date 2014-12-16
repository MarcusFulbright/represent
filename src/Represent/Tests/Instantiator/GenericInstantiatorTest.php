<?php

namespace Represent\Tests\Instantiator;

use Doctrine\Common\Collections\ArrayCollection;
use Represent\Instantiator\GenericInstantiator;
use Represent\Test\RepresentTestCase;

class GenericInstantiatorTest extends RepresentTestCase
{
    public function testInstantiate()
    {
        $reflection = new \ReflectionClass('Represent\Test\Fixtures\Adult');
        $data       = new \stdClass();
        $data->firstName = 'Harry';
        $data->lastName  = 'LockHart';
        $data->age       = 35;
        $data->publicTest = 'test';

        $instantiator = new GenericInstantiator();
        $result = $instantiator->instantiate($data, $reflection);

        $this->assertInstanceOf('Represent\Test\Fixtures\Adult', $result);
        $this->assertEquals('Harry', $result->getFirstName());
        $this->assertEquals('LockHart', $result->getLastName());
        $this->assertEquals(35, $result->getAge());
        $this->assertEquals('test', $result->publicTest);
        $this->assertEquals(new ArrayCollection(), $result->getChildren());
    }

    public function testSupportsInvalidBadProperty()
    {
        $paramName  = 'paramName';
        $data       = new \stdClass();

        $param = $this->getBasicReflectionParamMock();
        $param->shouldReceive('getName')->andReturn($paramName);
        $param->shouldReceive('isDefaultValueAvailable')->andReturn(false);

        $constructor = $this->getBasicReflectionMethodMock();
        $constructor->shouldReceive('getParameters')->andReturn(array($param));

        $reflection = $this->getBasicReflectionClassMock();
        $reflection->shouldReceive('getConstructor')->andReturn($constructor);

        $instantiator = new GenericInstantiator();
        $this->assertFalse($instantiator->supports($data, $reflection));
    }


    public function testSupportsInvalidNoProperty()
    {
        $paramName  = 'paramName';

        $data = new \stdClass();

        $param = $this->getBasicReflectionParamMock();
        $param->shouldReceive('getName')->andReturn($paramName);
        $param->shouldReceive('isDefaultValueAvailable')->andReturn(false);

        $constructor = $this->getBasicReflectionMethodMock();
        $constructor->shouldReceive('getParameters')->andReturn(array($param));

        $reflection = $this->getBasicReflectionClassMock();
        $reflection->shouldReceive('getConstructor')->andReturn($constructor);

        $instantiator = new GenericInstantiator();
        $this->assertFalse($instantiator->supports($data, $reflection));
    }


    public function testSupportsValidWithDefault()
    {
        $paramName  = 'paramName';
        $paramValue = 'Clover';

        $data = new \stdClass();

        $param = $this->getBasicReflectionParamMock();
        $param->shouldReceive('getName')->andReturn($paramName);
        $param->shouldReceive('canBePassedByValue')->with($paramValue)->andReturn(true);
        $param->shouldReceive('isDefaultValueAvailable')->andReturn(true);

        $constructor = $this->getBasicReflectionMethodMock();
        $constructor->shouldReceive('getParameters')->andReturn(array($param));

        $reflection = $this->getBasicReflectionClassMock();
        $reflection->shouldReceive('getConstructor')->andReturn($constructor);

        $instantiator = new GenericInstantiator();
        $this->assertTrue($instantiator->supports($data, $reflection));
    }


    public function testSupportsValidNoDefault()
    {
        $paramName  = 'paramName';
        $paramValue = 'Clover';

        $data = new \stdClass();
        $data->$paramName = $paramValue;

        $param = $this->getBasicReflectionParamMock();
        $param->shouldReceive('getName')->andReturn($paramName);
        $param->shouldReceive('canBePassedByValue')->with($paramValue)->andReturn(true);
        $param->shouldReceive('isDefaultValueAvailable')->andReturn(false);

        $constructor = $this->getBasicReflectionMethodMock();
        $constructor->shouldReceive('getParameters')->andReturn(array($param));

        $reflection = $this->getBasicReflectionClassMock();
        $reflection->shouldReceive('getConstructor')->andReturn($constructor);

        $instantiator = new GenericInstantiator();
        $this->assertTrue($instantiator->supports($data, $reflection));
    }


    public function testPrepareConstructorArgumentsDefaultValue()
    {
        $paramName  = 'paramName';
        $paramValue = 'Clover';
        $data       = new \stdClass();

        $param = $this->getBasicReflectionParamMock();
        $param->shouldReceive('getName')->andReturn($paramName);
        $param->shouldReceive('getDefaultValue')->andReturn($paramValue);

        $constructor = $this->getBasicReflectionMethodMock();
        $constructor->shouldReceive('getParameters')->andReturn(array($param));

        $instantiator = new GenericInstantiator();
        $result = $this->getReflectedMethod($instantiator, 'prepareConstructorArguments')->invoke($instantiator, $constructor, $data);

        $expected = array($paramValue);
        $this->assertEquals($expected, $result);
    }

    public function testPrepareConstructorArguments()
    {
        $paramName  = 'paramName';
        $paramValue = 'Clover';
        $data       = new \stdClass();
        $data->$paramName = $paramValue;

        $param = $this->getBasicReflectionParamMock();
        $param->shouldReceive('getName')->andReturn($paramName);

        $constructor = $this->getBasicReflectionMethodMock();
        $constructor->shouldReceive('getParameters')->andReturn(array($param));

        $instantiator = new GenericInstantiator();
        $result = $this->getReflectedMethod($instantiator, 'prepareConstructorArguments')->invoke($instantiator, $constructor, $data);

        $expected = array($paramValue);
        $this->assertEquals($expected, $result);
    }
}