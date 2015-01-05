<?php

namespace Represent\Tests\Factory;

use Represent\Factory\CollectionFactory;
use Represent\Test\RepresentTestCase;

class CollectionFactoryTest extends RepresentTestCase
{
    public function testCreateCollectionFromPager()
    {
        $pager       = $this->getPagerMock();
        $url         = 'my_url';
        $results     = array('something', 'somethingelse');
        $currentPage = 1;
        $nbPages     = 10;
        $nbResults   = 11;
        $limit       = 15;

        $pager->shouldReceive('getCurrentPageResults')->andReturn($results);
        $pager->shouldReceive('getCurrentPage')->andReturn($currentPage);
        $pager->shouldReceive('getNbPages')->andReturn($nbPages);
        $pager->shouldReceive('getNbResults')->andReturn($nbResults);
        $pager->shouldReceive('getMaxPerPage')->andReturn($limit);

        $factory = new CollectionFactory();
        $result  = $factory->createCollectionFromPager($pager, $url);

        $this->assertInstanceof('Represent\Util\PaginatedCollection', $result);
        $this->assertEquals($results, $result->getItems());
        $this->assertEquals($url, $result->getRoute());
        $this->assertEquals($nbPages, $result->getPages());
        $this->assertEquals($nbResults, $result->getTotal());
    }
}