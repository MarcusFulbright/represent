<?php

use represent\builder\GenericRepresentationBuilder;
use represent\tests\fixtures\Adult;
use represent\tests\fixtures\Child;
use represent\tests\fixtures\Toy;

class GenericRepresentationBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildObjectRepresentationSimple()
    {
        $parent  = new Adult('first', 'last', 20, 'public');
        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $expected = new \stdClass();
        $expected->firstName  = 'first';
        $expected->lastName   = 'last';
        $expected->age        = 20;
        $expected->publicTest = 'public';
        $expected->children   = array();

        $this->assertEquals($expected, $result);
    }

    public function testBuildObjectRepresentationWithChildren()
    {
        $parent = new Adult('first', 'last', 20, 'public');
        $parent->addChild(new Child('first', 'last'));

        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $childResult            = new \stdClass();
        $childResult->firstName = 'first';
        $childResult->lastName  = 'last';
        $childResult->toys      = array();

        $expected = new \stdClass();
        $expected->firstName = 'first';
        $expected->lastName  = 'last';
        $expected->age =        20;
        $expected->publicTest = 'public';
        $expected->children  = array ($childResult);

        $this->assertEquals($expected, $result);
    }

    public function testBuildObjectRepresentationWithNormalArray()
    {
        $parent = new Adult('first', 'last', 20, array(1,2,array(3,4)));
        $parent->addChild(new Child('first', 'last'));

        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $childResult = new \stdClass();
        $childResult->firstName = 'first';
        $childResult->lastName  = 'last';
        $childResult->toys      = array();

        $expected = new \stdClass();
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

    public function testBuildObjectRepresentationDeepNesting()
    {
        $child = new Child('first', 'last');
        $child->addToy(new Toy('red', 'joe', 'vhroom'));

        $parent = new Adult('first', 'last', 20, array(1,2,array(3,4)));
        $parent->addChild($child);

        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $toyResult = new \stdClass();
        $toyResult->color = 'red';
        $toyResult->name  = 'joe';
        $toyResult->sound = 'vhroom';

        $childResult = new \stdClass();
        $childResult->firstName = 'first';
        $childResult->lastName = 'last';
        $childResult->toys = array($toyResult);

        $expected = new \stdClass();
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

    public function testBuildObjectRepresentationWithObjectAsProperty()
    {
        $object = new Toy('red', 'car', 'vhroom');

        $parent  = new Adult('first', 'last', 20, $object);
        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $objectResult = new \stdClass();
        $objectResult->color = 'red';
        $objectResult->name = 'car';
        $objectResult->sound = 'vhroom';

        $expected = new \stdClass();
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
        $builder = new GenericRepresentationBuilder();
        $result = $builder->buildRepresentation(array($parent, $parent2));

        $parentResult = new \stdClass();
        $parentResult->firstName  = 'first';
        $parentResult->lastName   = 'last';
        $parentResult->age        = 20;
        $parentResult->publicTest = 'public';
        $parentResult->children   = array();

        $expected = array(
            $parentResult,
            $parentResult
        );

        $this->assertEquals($expected, $result);
    }

    public function testBuildRepresentationWithSimpleArray()
    {
        $test = array(1,2,3);
        $builder = new GenericRepresentationBuilder();
        $result = $builder->buildRepresentation($test);

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

        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildRepresentation(array($parent, $testKeys));

        $toyResult = new \stdClass();
        $toyResult->color = 'red';
        $toyResult->name  = 'joe';
        $toyResult->sound = 'vhroom';

        $childResult = new \stdClass();
        $childResult->firstName = 'first';
        $childResult->lastName = 'last';
        $childResult->toys = array($toyResult);

        $parentResult = new \stdClass();
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

    public function testBuildObjectRepresentationNullValues()
    {
        $parent  = new Adult(null, null, null, null);
        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildRepresentation($parent);

        $expected = new \stdClass();
        $expected->firstName  = null;
        $expected->lastName   = null;
        $expected->age        = null;
        $expected->publicTest = null;
        $expected->children   = array();

        $this->assertEquals($expected, $result);
    }
}