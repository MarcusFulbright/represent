<?php

namespace represent\tests\fixtures;

class ParentTest
{
    private $firstName;

    private $lastName;

    private $age;

    public $publicTest;

    public function __construct($firstName = null, $lastName = null, $age = null, $publicTest = null)
    {
        $this->firstName   = $firstName;
        $this->lastName    = $lastName;
        $this->age         = $age;
        $this->publicTest = $publicTest;
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
}