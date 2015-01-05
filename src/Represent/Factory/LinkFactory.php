<?php

namespace Represent\Factory;

use Represent\Annotations\Link;

class LinkFactory
{
    public function createLink($name, $uri, $parameters = array(), $views = array(), $absolute = false)
    {
        $link             = new Link();
        $link->name       = $name;
        $link->uri        = $uri;
        $link->parameters = $parameters;
        $link->views      = $views;
        $link->absolute   = $absolute;

        return $link;
    }
}