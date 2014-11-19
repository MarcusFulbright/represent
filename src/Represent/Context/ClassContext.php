<?php

namespace Represent\Context;

/**
  * Value object to hold meta data about the class
  */
class ClassContext
{
    public $policy;

    public $properties = array();

    public $group = null;
}