<?php

namespace Represent\Tests\Generator;

use Represent\Generator\LinkGenerator;
use Represent\Test\RepresentTestCase;

class LinkGeneratorTest extends RepresentTestCase
{
    public function testEvaluateExpression()
    {
        $haystack = "expr('object.getFirstName')";
        $language = $this->getExpressionLangaugeMock();
        $expected = 'Success!';
        $object   = new \stdClass();

        $language->shouldReceive('evaluate')->once()->with('object.getFirstName', array('object' => $object))->andReturn($expected);
        $generator = new LinkGenerator($this->getUrlGeneratorMock(), $language);
        $result    = $this->getReflectedMethod($generator, 'evaluateExpression')->invoke($generator, $haystack, $object);

        $this->assertEquals($expected, $result);
    }

    public function testParseParams()
    {
        $object = new \stdClass();
        $params = array(
            'id'       => 1,
            'clientID' => 2
        );
        $link = $this->getLinkMock();
        $link->parameters = $params;

        $generator = new LinkGenerator($this->getUrlGeneratorMock(), $this->getExpressionLangaugeMock());

        $this->assertEquals($params, $generator->parseparams($link, $object)->parameters);
    }
}
