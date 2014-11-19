<?php

namespace Represent\Test\Fixtures;

class Toy
{
    private $color;

    private $name;

    private $sound;

    public function __construct($color, $name, $sound)
    {
        $this->color = $color;
        $this->name  = $name;
        $this->sound = $sound;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $sound
     */
    public function setSound($sound)
    {
        $this->sound = $sound;
    }

    /**
     * @return mixed
     */
    public function getSound()
    {
        return $this->sound;
    }
}
