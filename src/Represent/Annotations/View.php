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
    * @Required
    */
    public $name = array();
}