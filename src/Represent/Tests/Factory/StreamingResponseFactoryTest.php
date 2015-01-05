<?php

namespace Represent\Tests\Factory;

use Represent\Factory\StreamingResponseFactory;
use Represent\Test\RepresentTestCase;

class StreamingResponseFactoryTest extends RepresentTestCase
{
    public function testCreateStreamingResponse()
    {
        $fileName = 'fakefilename.fake';
        $filePath = 'a/fake/file/path/' . $fileName;

        $factory = new StreamingResponseFactory($this->getPaginationFactoryMock());
        $response = $factory->createStreamingResponse($filePath, $fileName);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals('attachment; filename="' . $fileName . '"', $response->headers->get('Content-Disposition'));
        $this->assertEquals('application/octet-stream', $response->headers->get('Content-Type'));
    }
}