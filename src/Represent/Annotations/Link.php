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
    public $group = array();
    public $absolute = false;
}