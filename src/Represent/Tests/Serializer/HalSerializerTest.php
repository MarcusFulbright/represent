<?php

namespace Represent\Tests\Serializer;

use Represent\Builder\Format\HalFormatBuilder;
use Represent\Generator\LinkGenerator;
use Represent\Serializer\HalSerializer;
use Represent\Test\RepresentTestCase;
use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Test\Fixtures\Annotated\Adult;
use Represent\Test\Fixtures\Annotated\Child;
use Represent\Test\Fixtures\Annotated\Toy;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Generator\UrlGenerator;

class HalSerializerTest extends RepresentTestCase
{
    public function testToJsonWithAnnotations()
    {
        $toy    = new Toy('brown', 'Golem', 'smash');
        $child  = new Child('Henry', 'Parish');
        $child->addToy($toy);
        $parent = new Adult('Ichabod', 'Crane', '40', 'invisible');
        $parent->addChild($child);

        $urlGenerator      = $this->getUrlGeneratorMock();
        $genericBuilder    = $this->getGenericRepresentationBuilder();
        $halBuilder        = new HalFormatBuilder(new AnnotationReader(), new LinkGenerator($urlGenerator, new ExpressionLanguage()));
        $serializer        = new HalSerializer($halBuilder, $genericBuilder);

        $urlGenerator->shouldReceive('generate')->andReturn("www.example.com/selfLink");

        $result = $serializer->serialize($parent, 'hal+json');
        $expected = '{"_hash":0,"First Name":"Ichabod","Last Name":"Crane","_embedded":{"children":[{"_hash":1,"First Name":"Henry","Last Name":"Parish","toys":[{"_hash":2,"color":"brown","name":"Golem","sound":"smash"}]}]},"_links":{"self":"www.example.com/selfLink"}}';

        $this->assertEquals($expected, $result);
    }
}