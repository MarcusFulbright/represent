<?php

namespace Represent\Util;

use Represent\Annotations as Represent;

/**
 * @Represent\ExclusionPolicy(policy="whiteList")
 * @Represent\LinkCollection(links={
 *     @Represent\Link(
 *         name="self",
 *         uri="expr('object.getRoute()')",
 *         parameters="expr('object.getParameters()')"
 *     ),
 *     @Represent\Link(
 *         name="first",
 *         uri="expr('object.getRoute()')",
 *         parameters="expr('object.getParameters(1)')"
 *     ),
 *     @Represent\Link(
 *         name="last",
 *         uri="expr('object.getRoute()')",
 *         parameters="expr('object.getParameters(object.getPages())')"
 *     ),
 *     @Represent\Link(
 *         name="next",
 *         uri="expr('object.getRoute()')",
 *         parameters="expr('object.getParameters(object.getPages() + 1)')"
 *     ),
 *     @Represent\Link(
 *        name="previous",
 *        uri="expr('object.getRoute()')",
 *        parameters="expr('object.getParameters(object.getPages() - 1)')"
 *  )
 * })
 */
class PaginatedCollection
{
    /**
     * @Represent\Embedded
     */
    private $items;

    /**
     * @var int
     */
    private $pages;

    /**
     * @var int
     */
    private $total;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $page;

    /**
     * @Represent\Hide
     */
    private $route;

    /**
     * @var array
     */
    private $params;

    /**
     * @Represent\Hide
     */
    private $pageParam;

    /**
     * @Represent\Hide
     */
    private $limitParam;

    /**
     * @param        $items
     * @param        $page
     * @param        $pages
     * @param        $total
     * @param        $route
     * @param array  $params
     * @param int    $limit
     * @param string $pageParam
     * @param string $limitParam
     */
    public function __construct($items, $page, $pages, $total, $route, $params = array(), $limit = 10, $pageParam = 'page', $limitParam = 'limit')
    {
        $this->items      = $items;
        $this->page       = $page;
        $this->pages      = $pages;
        $this->total      = $total;
        $this->route      = $route;
        $this->params     = $params;
        $this->limit      = $limit;
        $this->pageParam  = $pageParam;
        $this->limitParam = $limitParam;

    }

    /**
     * Handles parsing through parameters array
     *
     * @param null $page
     * @param null $limit
     * @return array
     */
    public function getParameters($page = null, $limit = null)
    {
        $params = $this->params;

        $params[$this->pageParam]  = null == $page  ? $this->getPage()  : $page;
        $params[$this->limitParam] = null == $limit ? $this->getLimit() : $limit;

        return $params;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limitParam
     */
    public function setLimitParam($limitParam)
    {
        $this->limitParam = $limitParam;
    }

    /**
     * @return mixed
     */
    public function getLimitParam()
    {
        return $this->limitParam;
    }

    /**
     * @param mixed $pageParam
     */
    public function setPageParam($pageParam)
    {
        $this->pageParam = $pageParam;
    }

    /**
     * @return mixed
     */
    public function getPageParam()
    {
        return $this->pageParam;
    }

    /**
     * @param mixed $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }
}
