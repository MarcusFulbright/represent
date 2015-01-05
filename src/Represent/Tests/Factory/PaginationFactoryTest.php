<?php

namespace Represent\Tests\Factory;

use Represent\Factory\PaginationFactory;
use Represent\Test\RepresentTestCase;

class PaginationFactoryTest extends RepresentTestCase
{
    public function testMakePagerFromArray()
    {
        $data    = array(1, 2, 3);
        $factory = new PaginationFactory($this->getCollectionFactoryMock());
        $pager   = $this->getReflectedMethod($factory, 'makePagerFromArray')->invokeArgs($factory, array($data));

        $this->assertEquals($data, $pager->getCurrentPageResults());
        $this->assertEquals(count($data), $pager->getNbResults());
    }

    public function testPaginate()
    {
        $data    = array(1,2,3,4);
        $page    = 1;
        $limit   = 10;
        $url     = 'my_url';
        $factory = $this->getCollectionFactoryMock();

        $factory->shouldReceive('createCollectionFromPager')->andReturn('success');
        $pagination = new PaginationFactory($factory);

        $this->assertEquals('success', $pagination->paginate($data, $page, $limit, $url));
    }
}