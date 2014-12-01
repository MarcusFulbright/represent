<?php

namespace Represent\Tests\Factory;

use Represent\Factory\ResponseFactory;
use Represent\Test\RepresentTestCase;

class ResponseFactoryTest extends RepresentTestCase
{
    public function testCreateStreamingResponse()
    {
        $fileName = 'fakefilename.fake';
        $filePath = 'a/fake/file/path/' . $fileName;

        $factory = new ResponseFactory($this->getPaginationFactoryMock());
        $response = $factory->createStreamingResponse($filePath, $fileName);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals('attachment; filename="' . $fileName . '"', $response->headers->get('Content-Disposition'));
        $this->assertEquals('application/octet-stream', $response->headers->get('Content-Type'));
    }

    public function testPreparePagination()
    {
        $data              = array(1,2,3,4);
        $link              = $this->getLinkMock();
        $paginationFactory = $this->getPaginationFactoryMock();

        $paginationFactory->shouldReceive('paginatedRepresentation')->once();
        $paginationFactory->shouldReceive('makePagerFromArray')->with($data, 1, 10);

        $factory = new ResponseFactory($paginationFactory);
    }
}