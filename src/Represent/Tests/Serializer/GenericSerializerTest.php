<?php

namespace Represent\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Builder\ClassContextBuilder;
use Represent\Builder\GenericRepresentationBuilder;
use Represent\Builder\PropertyContextBuilder;
use Represent\Serializer\GenericSerializer;
use Represent\Test\RepresentTestCase;
use Represent\Test\Fixtures\Annotated\Adult;
use Represent\Test\Fixtures\Annotated\Child;
use Represent\Test\Fixtures\Annotated\Toy;

class GenericSerializerTest extends RepresentTestCase
{
    public function testToJsonWithAnnotations()
    {
        $toy    = new Toy('brown', 'Golem', 'smash');
        $child  = new Child('Henry', 'Parish');
        $child->addToy($toy);
        $parent = new Adult('Ichabod', 'Crane', '40', 'invisible');
        $parent->addChild($child);

        $reader     = new AnnotationReader();
        $builder    = new GenericRepresentationBuilder(new PropertyContextBuilder($reader), new ClassContextBuilder($reader));
        $serializer = new GenericSerializer($builder);

        $result = $serializer->toJson($parent);
        $expected = '{"First Name":"Ichabod","Last Name":"Crane","children":[{"First Name":"Henry","Last Name":"Parish","toys":[{"color":"brown","name":"Golem","sound":"smash"}]}]}';

        $this->assertEquals($expected, $result);
    }

    public function testToJsonWithAnnotationsAndView()
    {
        $toy    = new Toy('brown', 'Golem', 'smash');
        $child  = new Child('Henry', 'Parish');
        $child->addToy($toy);
        $parent = new Adult('Ichabod', 'Crane', '40', 'invisible');
        $parent->addChild($child);

        $reader     = new AnnotationReader();
        $builder    = new GenericRepresentationBuilder(new PropertyContextBuilder($reader), new ClassContextBuilder($reader));
        $serializer = new GenericSerializer($builder);

        $result = $serializer->toJson($parent, 'private');
        $expected = '{"First Name":"Ichabod","Last Name":"Crane","age":40,"children":[{"First Name":"Henry","Last Name":"Parish","toys":[{"color":"brown","name":"Golem","sound":"smash"}]}]}';

        $this->assertEquals($expected, $result);
    }
}