<?php

namespace Represent\Test;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Builder\ClassContextBuilder;
use Represent\Builder\GenericRepresentationBuilder;
use Represent\Builder\PropertyContextBuilder;
use Represent\Handler\PropertyHandler;

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

    protected function getBasicReflectionMethodMock()
    {
        return \Mockery::mock('\ReflectionMethod');
    }

    protected function getBasicReflectionParamMock()
    {
        return \Mockery::mock('\ReflectionParameter');
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

    protected function getRepresentSerializerInterfaceMock()
    {
        return \Mockery::mock('Represent\Serializer\RepresentSerializerInterface');
    }

    protected function getExpressionLangaugeMock()
    {
        return \Mockery::mock('Symfony\Component\ExpressionLanguage\ExpressionLanguage');
    }

    protected function getPagerMock()
    {
        return \Mockery::mock('Pagerfanta\Pagerfanta');
    }

    protected function getPaginationFactoryMock()
    {
        return \Mockery::mock('Represent\Factory\PaginationFactory');
    }

    protected function getEntityManagerMock()
    {
        return \Mockery::mock('Doctrine\ORM\EntityManager');
    }

    protected function getDoctrineConnectionMock()
    {
        return \Mockery::mock('Doctrine\DBAL\Connection');
    }

    protected function getConfigurationMocK()
    {
        return \Mockery::mock('Doctrine\ORM\Configuration');
    }

    protected function getEventManagerMock()
    {
        return \Mockery::mock('Doctrine\Common\EventManager');
    }

    protected  function getGenericRepresentationBuilder()
    {
        $reader = new AnnotationReader();

        return new GenericRepresentationBuilder(new PropertyContextBuilder(new PropertyHandler($reader)), new ClassContextBuilder($reader));
    }

    protected function getGenericInstantiatorMock()
    {
        return \Mockery::mock('Represent\Instantiator\GenericInstantiator');
    }

    protected function getDoctrineClassMetaMock()
    {
        return \Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
    }

    protected function getPropertyHandlerMock()
    {
        return \Mockery::mock('Represent\Handler\PropertyHandler');
    }

    protected function getPropertyBuilderMock()
    {
        return \Mockery::mock('Represent\Builder\PropertyContextBuilder');
    }

    protected function getClassBuilderMock()
    {
        return \Mockery::mock('Represent\Builder\ClassContextBuilder');
    }

    protected function getCollectionFactoryMock()
    {
        return \Mockery::mock('Represent\Factory\CollectionFactory');
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
