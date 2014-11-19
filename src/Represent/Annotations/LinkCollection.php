<?php

namespace Represent\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class LinkCollection 
{
    public $links = array();
}