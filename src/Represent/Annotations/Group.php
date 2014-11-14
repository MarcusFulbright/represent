<?php

namespace Represent\Annotations;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Group 
{
   /**
    * @var array
    */
    public $name;
}