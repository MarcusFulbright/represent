<?php

namespace Represent\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class View
{
   /**
    * @var array
    */
    public $name = array();
}