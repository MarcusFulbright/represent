<?php

use represent\builder\GenericRepresentationBuilder;
use represent\tests\fixtures\ParentTest;

class GenericRepresentationBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildObjectRepresentationSimple()
    {
        $parent  = new ParentTest('first', 'last', 20, 'public');
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
        $parent = new ParentTest('first', 'last', 20, 'public');
        $parent->addChild(new \represent\tests\fixtures\ChildTest('first', 'last'));

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
}