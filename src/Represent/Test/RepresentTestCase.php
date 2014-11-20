<?php

namespace Represent\Test;

class RepresentTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getBasicReflectionClassMock()
    {
        return \Mockery::mock('\ReflectionClass');
    }

    protected function getClassContextMock()
    {
        return \Mockery::mock('Represent\Context\ClassContext');
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

    protected function getViewMock()
    {
        return \Mockery::mock('Represent\Annotations\View');
    }

    protected function getPropertyMock()
    {
        return \Mockery::mock('Represent\Annotations\Property');
    }

    protected function getPropertyContextMock()
    {
        return \Mockery::mock('Represent\Context\PropertyContext');
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

    protected function getUrlGeneratorMock()
    {
        return \Mockery::mock('Symfony\Component\Routing\Generator\UrlGenerator');
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