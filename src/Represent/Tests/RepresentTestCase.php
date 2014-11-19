<?php

namespace Represent\Tests;

class RepresentTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getBasicReflectionClassMock()
    {
        return \Mockery::mock('\ReflectionClass');
    }

    protected function getClassMetaDataMock()
    {
        return \Mockery::mock('Represent\MetaData\ClassMetaData');
    }

    protected function getAnnotationReaderMock()
    {
        return \Mockery::mock('Doctrine\Common\Annotations\AnnotationReader');
    }

    protected function getBasicReflectionPropertyMock()
    {
        return \Mockery::mock('\ReflectionProperty');
    }

    protected function getHideMock()
    {
        return \Mockery::mock('Represent\Annotations\Hide');
    }

    protected function getExclusionPolicyMock()
    {
        return \Mockery::mock('Represent\Annotations\ExclusionPolicy');
    }

    protected function getShowMock()
    {
        return \Mockery::mock('Represent\Annotations\Show');
    }

    protected function getGroupMock()
    {
        return \Mockery::mock('Represent\Annotations\Group');
    }

    protected function getPropertyMock()
    {
        return \Mockery::mock('Represent\Annotations\Property');
    }

    protected function getPropertyMetaDataMock()
    {
        return \Mockery::mock('Represent\MetaData\PropertyMetaData');
    }

    protected function getLinkCollectionMock()
    {
        return \Mockery::mock('Represent\Annotations\LinkCollection');
    }

    protected function getLinkMock()
    {
        return \Mockery::mock('Represent\Annotations\Link');
    }

    protected function getLinkGeneratorMock()
    {
        return \Mockery::mock('Represent\Generator\LinkGenerator');
    }

    protected function getEmbeddedMock()
    {
        return \Mockery::mock('Represent\Annotations\Embedded');
    }

    /**
     * Creates a reflection method and makes it accessible
     *
     * @param $baseClass
     * @param $methodName
     * @return \ReflectionMethod
     */
    protected function getReflectedMethod($baseClass, $methodName)
    {
        $class  = new \ReflectionClass($baseClass);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }
}