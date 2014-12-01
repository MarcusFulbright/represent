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
     * @var \Represent\Generator\LinkGenerator
     */
    private $linkGenerator;

    /**
     * @param \Represent\Generator\LinkGenerator $linkGenerator
     */
    public function __construct(LinkGenerator $linkGenerator)
    {
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * Creates a pager from an array
     *
     * @param     $data
     * @param int $page
     * @param int $limit
     * @return Pagerfanta
     */
    public function makePagerFromArray($data, $page = 1, $limit = 10)
    {
        $adapter = new ArrayAdapter($data);
        $pager   = new Pagerfanta($adapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }

    /**
     * Creates a paginated representation from a pager
     *
     * @param Pagerfanta $pager
     * @param Link       $link
     * @return PaginatedCollection
     */
    public function paginatedRepresentation(Pagerfanta $pager, Link $link)
    {
        return new PaginatedCollection(
            $pager->getCurrentPageResults(),
            $pager->getCurrentPage(),
            $pager->getNbPages(),
            $pager->getNbResults(),
            $this->linkGenerator->parseName($link),
            $link->parameters
        );
    }
}