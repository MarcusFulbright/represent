<?php

namespace Represent\Annotations;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Link 
{
    /**
     * @Required
     */
    public $name;

    /**
     * @Required
     */
    public $uri;

    public $parameters = array();

    public $views      = array();

    public $absolute   = false;
}