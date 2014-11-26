<?php

namespace Represent\Tests\Serializer;

use Represent\Serializer\MasterSerializer;
use Represent\Test\RepresentTestCase;

class MasterSerializerTest extends RepresentTestCase
{
    public function testSerializersMustHaveInterface()
    {
        $formatMap = array(
            'json' => $this->getUrlGeneratorMock()
        );

        $this->setExpectedException('Exception', 'Serializers must implement MySerializerInterface');
        new MasterSerializer($formatMap);
    }

    public function testCanOnlySerializeConfiguredFormats()
    {
        $format    = 'wrongOne';
        $formatMap = array(
            'json' => $this->getRepresentSerializerInterfaceMock()
        );
        $serializer = new MasterSerializer($formatMap);

        $this->setExpectedException('Exception', $format.' is not configured');
        $serializer->serialize($this->getUrlGeneratorMock(), $format);
    }

    public function testSerializeSuccess()
    {
        $object = $this->getUrlGeneratorMock();
        $format = 'json';
        $view   = null;
        $serializer = $this->getRepresentSerializerInterfaceMock();
        $serializer->shouldReceive('serialize')->with($object, $format, $view)->andReturn('Success!');
        $formatMap = array($format => $serializer);

        $master = new MasterSerializer($formatMap);
        $this->assertEquals('Success!', $master->serialize($object, $format, $view));
    }
}