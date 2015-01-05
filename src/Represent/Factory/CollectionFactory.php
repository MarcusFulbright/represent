<?php

namespace Represent\Factory;

use Pagerfanta\Pagerfanta;
use Represent\Util\PaginatedCollection;

class CollectionFactory
{
    public function createCollectionFromPager(Pagerfanta $pager, $url, $params = array())
    {
        return new PaginatedCollection(
            $pager->getCurrentPageResults(),
            $pager->getCurrentPage(),
            $pager->getNbPages(),
            $pager->getNbResults(),
            $url,
            $params,
            $pager->getMaxPerPage()
        );
    }
}