<?php

namespace Represent\Annotations;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class Link 
{
    public $name;
    public $uri;
    public $parameters = array();
    public $views      = array();
    public $absolute   = false;
}