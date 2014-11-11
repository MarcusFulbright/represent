<?php

use represent\builder\GenericRepresentationBuilder;
use represent\tests\fixtures\Adult;
use represent\tests\fixtures\Child;

class GenericRepresentationBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildObjectRepresentationSimple()
    {
        $parent  = new Adult('first', 'last', 20, 'public');
        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildObjectRepresentation($parent);

        $expected = array(
            'firstName'  => 'first',
            'lastName'   => 'last',
            'age'        => 20,
            'publicTest' => 'public',
            'children'   => array()
        );

        $this->assertEquals($expected, $result);
    }

    public function testBuildObjectRepresentationWithChildren()
    {
        $parent = new Adult('first', 'last', 20, 'public');
        $parent->addChild(new Child('first', 'last'));

        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildObjectRepresentation($parent);

        $expected = array(
            'firstName' => 'first',
            'lastName'  => 'last',
            'age'       => 20,
            'publicTest'=> 'public',
            'children'  => array(
                0 => array(
                    'firstName' => 'first',
                    'lastName'  => 'last'
                )
            )
        );

        $this->assertEquals($expected, $result);
    }

    public function testBuildObjectRepresentationWithNormalArray()
    {
        $parent = new Adult('first', 'last', 20, array(1,2,array(3,4)));
        $parent->addChild(new Child('first', 'last'));

        $builder = new GenericRepresentationBuilder();
        $result  = $builder->buildObjectRepresentation($parent);

        $expected = array(
            'firstName' => 'first',
            'lastName'  => 'last',
            'age'       => 20,
            'publicTest' => array(
                1,
                2,
                array(
                    3,4
                ),
            ),
            'children' => array(
                0 => array(
                    'firstName' => 'first',
                    'lastName'  => 'last'
                )
            )

        );

        $this->assertEquals($expected, $result);
    }
}