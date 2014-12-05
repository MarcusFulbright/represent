<?php

namespace Represent\Tests\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Test\RepresentTestCase;
use Represent\Test\Fixtures\Adult;
use Represent\Test\Fixtures\Child;
use Represent\Test\Fixtures\Toy;

class GenericRepresentationBuilderTest extends RepresentTestCase
{
    public function testBuildRepresentationCircularReference()
    {
        $parent = new Adult('first', 'last', 20, array(1,2,array(3,4)));

        $child = new Child('first', 'last', $parent);
        $child->addToy(new Toy('red', 'joe', 'vhroom'));

        $parent->addChild($child);

        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $toyResult        = new \stdClass();
        $toyResult->color = 'red';
        $toyResult->name  = 'joe';
        $toyResult->sound = 'vhroom';
        $toyResult->_hash = 2;

        $relResult = new \stdClass();
        $name  = '$rel';
        $relResult->$name = 0;

        $childResult            = new \stdClass();
        $childResult->firstName = 'first';
        $childResult->lastName  = 'last';
        $childResult->toys      = array($toyResult);
        $childResult->_hash     = 1;
        $childResult->parent    = $relResult;

        $expected             = new \stdClass();
        $expected->_hash      = 0;
        $expected->firstName  = 'first';
        $expected->lastName   = 'last';
        $expected->age        = 20;
        $expected->publicTest = array(
            1,
            2,
            array(
                3,4
            ),
        );
        $expected->children = array($childResult);

        $this->assertEquals($expected, $result);
    }

    public function testBuildRepresentationSimple()
    {
        $parent  = new Adult('first', 'last', 20, 'public');
        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $expected = new \stdClass();
        $expected->firstName  = 'first';
        $expected->lastName   = 'last';
        $expected->age        = 20;
        $expected->publicTest = 'public';
        $expected->children   = array();
        $expected->_hash      = 0;

        $this->assertEquals($expected, $result);
    }

    public function testBuildRepresentationWithChildren()
    {
        $parent = new Adult('first', 'last', 20, 'public');
        $parent->addChild(new Child('first', 'last'));

        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $childResult            = new \stdClass();
        $childResult->firstName = 'first';
        $childResult->lastName  = 'last';
        $childResult->toys      = array();
        $childResult->_hash     = 1;
        $childResult->parent    = null;

        $expected             = new \stdClass();
        $expected->firstName  = 'first';
        $expected->lastName   = 'last';
        $expected->age        =        20;
        $expected->publicTest = 'public';
        $expected->children   = array ($childResult);
        $expected->_hash      = 0;

        $this->assertEquals($expected, $result);
    }

    public function testBuildRepresentationWithNormalArray()
    {
        $parent = new Adult('first', 'last', 20, array(1,2,array(3,4)));
        $parent->addChild(new Child('first', 'last'));

        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $childResult = new \stdClass();
        $childResult->firstName = 'first';
        $childResult->lastName  = 'last';
        $childResult->toys      = array();
        $childResult->_hash     = 1;
        $childResult->parent    = null;

        $expected = new \stdClass();
        $expected->_hash = 0;
        $expected->firstName = 'first';
        $expected->lastName  = 'last';
        $expected->age       = 20;
        $expected->publicTest = array(
            1,
            2,
            array(
                3,4
            ),
        );
        $expected->children = array($childResult);

        $this->assertEquals($expected, $result);
    }

    public function testBuildRepresentationDeepNesting()
    {
        $child = new Child('first', 'last');
        $child->addToy(new Toy('red', 'joe', 'vhroom'));

        $parent = new Adult('first', 'last', 20, array(1,2,array(3,4)));
        $parent->addChild($child);

        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $toyResult = new \stdClass();
        $toyResult->color = 'red';
        $toyResult->name  = 'joe';
        $toyResult->sound = 'vhroom';
        $toyResult->_hash = 2;

        $childResult = new \stdClass();
        $childResult->firstName = 'first';
        $childResult->lastName = 'last';
        $childResult->toys = array($toyResult);
        $childResult->_hash = 1;
        $childResult->parent = null;

        $expected = new \stdClass();
        $expected->_hash = 0;
        $expected->firstName = 'first';
        $expected->lastName  = 'last';
        $expected->age       = 20;
        $expected->publicTest = array(
            1,
            2,
            array(
                3,4
            ),
        );
        $expected->children = array($childResult);

        $this->assertEquals($expected, $result);
    }

    public function testBuildRepresentationWithObjectAsProperty()
    {
        $object  = new Toy('red', 'car', 'vhroom');
        $parent  = new Adult('first', 'last', 20, $object);
        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $objectResult        = new \stdClass();
        $objectResult->color = 'red';
        $objectResult->name  = 'car';
        $objectResult->sound = 'vhroom';
        $objectResult->_hash = 1;

        $expected             = new \stdClass();
        $expected->_hash      = 0;
        $expected->firstName  = 'first';
        $expected->lastName   = 'last';
        $expected->publicTest = $objectResult;
        $expected->age        = 20;
        $expected->children   = array();

        $this->assertEquals($expected, $result);
    }

    public function testBuildObjectArrayWithObjects()
    {
        $parent  = new Adult('first', 'last', 20, 'public');
        $parent2 = new Adult('first', 'last', 20, 'public');
        $builder = $this->getGenericRepresentationBuilder();
        $result = $builder->buildRepresentation(array($parent, $parent2));

        $parentResult = new \stdClass();
        $parentResult->firstName  = 'first';
        $parentResult->lastName   = 'last';
        $parentResult->age        = 20;
        $parentResult->publicTest = 'public';
        $parentResult->children   = array();

        $parentResult2 = clone($parentResult);
        $parentResult2->_hash = 1;
        $parentResult->_hash  = 0;

        $expected = array(
            $parentResult,
            $parentResult2
        );

        $this->assertEquals($expected, $result);
    }

    public function testBuildRepresentationWithSimpleArray()
    {
        $test    = array(1,2,3);
        $builder =  $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($test);

        $this->assertEquals($test, $result);
    }

    public function testBuildRepresentationWithComplexArray()
    {
        $child = new Child('first', 'last');
        $child->addToy(new Toy('red', 'joe', 'vhroom'));

        $parent = new Adult('first', 'last', 20, array(1,2,array(3,4)));
        $parent->addChild($child);

        $testKeys = array(
            'key1' => 1,
            'key2' => 2,
            'key3' => 3
        );

        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation(array($parent, $testKeys));

        $toyResult = new \stdClass();
        $toyResult->color = 'red';
        $toyResult->name  = 'joe';
        $toyResult->sound = 'vhroom';
        $toyResult->_hash = 2;

        $childResult = new \stdClass();
        $childResult->firstName = 'first';
        $childResult->lastName = 'last';
        $childResult->toys = array($toyResult);
        $childResult->_hash = 1;
        $childResult->parent = null;

        $parentResult = new \stdClass();
        $parentResult->_hash = 0;
        $parentResult->children  = array($childResult);
        $parentResult->firstName = 'first';
        $parentResult->lastName  = 'last';
        $parentResult->age       = 20;
        $parentResult->publicTest = array(
            1,
            2,
            array(
                3,4
            ),
        );

        $expected = array($parentResult, $testKeys);

        $this->assertEquals($expected, $result);
    }

    public function testBuildRepresentationNullValues()
    {
        $parent  = new Adult(null, null, null, null);
        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $expected = new \stdClass();
        $expected->firstName  = null;
        $expected->lastName   = null;
        $expected->age        = null;
        $expected->publicTest = null;
        $expected->children   = array();
        $expected->_hash      = 0;

        $this->assertEquals($expected, $result);
    }

    public function testCanBuildRepresentationForPrimitiveString()
    {
        $test    = 'I am a string';
        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($test);

        $this->assertEquals($test, $result);
    }

    public function testCanBuildRepresentationForPrimitiveInteger()
    {
        $test    = 1;
        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($test);

        $this->assertEquals($test, $result);
    }

    public function testCanBuildRepresentationForPrimitiveBool()
    {
        $test    = true;
        $builder = $this->getGenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($test);

        $this->assertEquals($test, $result);
    }
}
