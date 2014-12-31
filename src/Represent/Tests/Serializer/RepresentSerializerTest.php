<?php

namespace Represent\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Serializer\GenericSerializer;
use Represent\Serializer\RepresentSerializer;
use Represent\Test\RepresentTestCase;
use Represent\Test\Fixtures\Annotated\Adult;
use Represent\Test\Fixtures\Annotated\Child;
use Represent\Test\Fixtures\Annotated\Toy;

class RepresentSerializerTest extends RepresentTestCase
{
    public function testToJsonWithAnnotations()
    {
        $toy    = new Toy('brown', 'Golem', 'smash');
        $child  = new Child('Henry', 'Parish');
        $child->addToy($toy);
        $parent = new Adult('Ichabod', 'Crane', '40', 'invisible');
        $parent->addChild($child);

        $builder    = $this->getGenericRepresentationBuilder();
        $serializer = new RepresentSerializer($builder);

        $result = $serializer->serialize($parent, 'json');
        $expected = '{"_hash":1,"First Name":"Ichabod","Last Name":"Crane","children":[{"_hash":2,"First Name":"Henry","Last Name":"Parish","toys":[{"_hash":3,"color":"brown","name":"Golem","sound":"smash"}]}]}';

        $this->assertEquals($expected, $result);
    }

    public function testToJsonWithAnnotationsAndView()
    {
        $toy    = new Toy('brown', 'Golem', 'smash');
        $child  = new Child('Henry', 'Parish');
        $child->addToy($toy);
        $parent = new Adult('Ichabod', 'Crane', '40', 'invisible');
        $parent->addChild($child);

        $builder    = $this->getGenericRepresentationBuilder();
        $serializer = new RepresentSerializer($builder);

        $result = $serializer->serialize($parent, 'json', 'private');
        $expected = '{"_hash":1,"First Name":"Ichabod","Last Name":"Crane","age":40,"children":[{"_hash":2,"First Name":"Henry","Last Name":"Parish","toys":[{"_hash":3,"color":"brown","name":"Golem","sound":"smash"}]}]}';

        $this->assertEquals($expected, $result);
    }
}