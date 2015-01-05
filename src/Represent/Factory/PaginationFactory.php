<?php

namespace Represent\Factory;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Represent\Annotations\Link;
use Represent\Generator\LinkGenerator;
use Represent\Util\PaginatedCollection;


class PaginationFactory
{
    /**
     * @var CollectionFactory
     */
    private $factory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->factory = $collectionFactory;
    }

    /**
     * Creates a pager from an array
     *
     * @param     $data
     * @param int $page
     * @param int $limit
     * @return Pagerfanta
     */
    protected function makePagerFromArray($data, $page = 1, $limit = 10)
    {
        $adapter = new ArrayAdapter($data);
        $pager   = new Pagerfanta($adapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }

    /**
     * Handles creating a pager and turning it into a collection representation
     *
     * @param       $data
     * @param       $page
     * @param       $limit
     * @param       $url
     * @param array $params
     * @return PaginatedCollection
     */
    public function paginate(array $data, $page, $limit , $url, $params = array())
    {
        $pager = $this->makePagerFromArray($data, $page, $limit);

        return $this->factory->createCollectionFromPager($pager, $url, $params);
    }
}