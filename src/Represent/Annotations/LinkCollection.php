<?php

namespace Represent\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class LinkCollection 
{
    /**
     * @Required
     */
    public $links = array();
}