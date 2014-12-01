<?php

namespace Represent\Tests\Factory;

use Represent\Factory\PaginationFactory;
use Represent\Test\RepresentTestCase;

class PaginationFactoryTest extends RepresentTestCase
{
    public function testMakePagerFromArray()
    {
        $data = array(
            1,
            2,
            3,
        );

        $factory = new PaginationFactory($this->getLinkGeneratorMock());
        $pager   = $factory->makePagerFromArray($data);

        $this->assertEquals($data, $pager->getCurrentPageResults());
        $this->assertEquals(count($data), $pager->getNbResults());
    }

    public function testPaginatedRepresentation()
    {
        $pager     = $this->getPagerMock();
        $link      = $this->getLinkMock();
        $generator = $this->getLinkGeneratorMock();

        $pager->shouldReceive('getCurrentPageResults')->once();
        $pager->shouldReceive('getCurrentPage')->once();
        $pager->shouldReceive('getNbPages')->once();
        $pager->shouldReceive('getNbResults')->once();

        $link->parameters = array();

        $generator->shouldReceive('parseName')->once()->with($link);

        $factory = new PaginationFactory($generator);
        $this->assertInstanceOf('Represent\Util\PaginatedCollection', $factory->paginatedRepresentation($pager, $link));
    }
}