<?php

namespace Represent\Tests\Serializer;

use Represent\Builder\ClassContextBuilder;
use Represent\Builder\Format\HalFormatBuilder;
use Represent\Builder\PropertyContextBuilder;
use Represent\Generator\LinkGenerator;
use Represent\Handler\PropertyHandler;
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
    private function getHalFormatBuilder($generator = null)
    {
        $generator = $generator === null ? $this->getUrlGeneratorMock() : $generator;

        $reader = new AnnotationReader();

        $config = array(
            'reader'        => $reader,
            'linkGenerator' =>  new LinkGenerator($generator, new ExpressionLanguage())
        );

        return new HalFormatBuilder(new PropertyContextBuilder(new PropertyHandler($reader)), new ClassContextBuilder($reader), $config);
    }

    public function testToJsonWithAnnotations()
    {
        $toy    = new Toy('brown', 'Golem', 'smash');
        $child  = new Child('Henry', 'Parish');
        $child->addToy($toy);
        $parent = new Adult('Ichabod', 'Crane', '40', 'invisible');
        $parent->addChild($child);

        $urlGenerator = $this->getUrlGeneratorMock();
        $halBuilder   = $this->getHalFormatBuilder($urlGenerator);
        $serializer   = new HalSerializer($halBuilder);

        $urlGenerator->shouldReceive('generate')->andReturn("www.example.com/selfLink");

        $result = $serializer->serialize($parent, 'hal+json');
        $expected = '{"_hash":1,"First Name":"Ichabod","Last Name":"Crane","_embedded":{"children":[{"_hash":2,"First Name":"Henry","Last Name":"Parish","_embedded":{"toys":[{"_hash":3,"color":"brown","name":"Golem","sound":"smash","_embedded":{},"_links":{}}]},"_links":{}}]},"_links":{"self":"www.example.com/selfLink"}}';

        $this->assertEquals($expected, $result);
    }

    public function testToJsonHandleArrayOfObjects()
    {
        $toy    = new Toy('brown', 'Golem', 'smash');
        $child  = new Child('Henry', 'Parish');
        $child->addToy($toy);
        $parent = new Adult('Ichabod', 'Crane', '40', 'invisible');
        $parent->addChild($child);

        $urlGenerator = $this->getUrlGeneratorMock();
        $halBuilder   = $this->getHalFormatBuilder($urlGenerator);
        $serializer   = new HalSerializer($halBuilder);

        $urlGenerator->shouldReceive('generate')->andReturn("www.example.com/selfLink");

        $result   = $serializer->serialize(array($parent), 'hal+json');
        $expected = '[{"_hash":1,"First Name":"Ichabod","Last Name":"Crane","_embedded":{"children":[{"_hash":2,"First Name":"Henry","Last Name":"Parish","_embedded":{"toys":[{"_hash":3,"color":"brown","name":"Golem","sound":"smash","_embedded":{},"_links":{}}]},"_links":{}}]},"_links":{"self":"www.example.com/selfLink"}}]';

        $this->assertEquals($expected, $result);
    }
}