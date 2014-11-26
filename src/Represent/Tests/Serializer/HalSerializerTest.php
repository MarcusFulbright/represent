<?php

namespace Represent\Tests\Serializer;

use Represent\Builder\Format\HalFormatBuilder;
use Represent\Generator\LinkGenerator;
use Represent\Serializer\HalSerializer;
use Represent\Test\RepresentTestCase;
use Doctrine\Common\Annotations\AnnotationReader;
use Represent\Builder\ClassContextBuilder;
use Represent\Builder\GenericRepresentationBuilder;
use Represent\Builder\PropertyContextBuilder;
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
        $reader            = new AnnotationReader();
        $genericBuilder    = new GenericRepresentationBuilder(new PropertyContextBuilder($reader), new ClassContextBuilder($reader));
        $halBuilder        = new HalFormatBuilder($reader, new LinkGenerator($urlGenerator, new ExpressionLanguage()));
        $serializer        = new HalSerializer($halBuilder, $genericBuilder);

        $urlGenerator->shouldReceive('generate')->andReturn('www.example.com/selfLink');

        $result = $serializer->serialize($parent, 'hal+json');
        $expected = '{"First Name":"Ichabod","Last Name":"Crane","_embedded":{"children":[{"First Name":"Henry","Last Name":"Parish","toys":[{"color":"brown","name":"Golem","sound":"smash"}]}]},"_links":{"self":"www.example.com\/selfLink"}}';

        $this->assertEquals($expected, $result);
    }
}