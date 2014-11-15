<?php

namespace Represent\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;

class Child
{
    private $firstName;

    private $lastName;

    private $toys;

    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
        $this->toys      = new ArrayCollection();
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
     * @param Toy $toy
     */
    public function addToy(Toy $toy)
    {
        $this->toys->add($toy);
    }

}