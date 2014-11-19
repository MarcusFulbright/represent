<?php

namespace Represent\Test\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;

use Represent\Annotations as Represent;

class Adult
{
    private $firstName;

    private $lastName;

    private $age;

    public $publicTest;

    private $children;

    public function __construct($firstName = null, $lastName = null, $age = null, $publicTest = null)
    {
        $this->firstName    = $firstName;
        $this->lastName     = $lastName;
        $this->age          = $age;
        $this->publicTest   = $publicTest;
        $this->children     = new ArrayCollection();
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Child $child
     */
    public function  addChild(Child $child)
    {
        $this->children->add($child);
    }
}