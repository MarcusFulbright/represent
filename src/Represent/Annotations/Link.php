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

    /**
     * @var array
     */
    public $parameters = array();

    /**
     * @var array
     */
    public $views = array();

    /**
     * @var bool
     */
    public $absolute = false;
}