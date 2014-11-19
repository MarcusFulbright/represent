<?php

namespace Represent\Test\Fixtures\Annotated;

use Doctrine\Common\Collections\ArrayCollection;
use Represent\Annotations as Represent;

/**
 * @Represent\ExclusionPolicy(policy="whiteList")
 * @Represent\LinkCollection(links={
 *    @Represent\Link(
 *         name="self",
 *         uri="mybundle_adult_getadultaction",
 *         parameters={"id" = "expr('object.getFirstName')" }
 *     ),
 * })
 */
class Adult
{
    /**
     * @Represent\Property(name="First Name")
     */
    private $firstName;

    /**
     * @Represent\Property(name="Last Name")
     */
    private $lastName;

    /**
     * @Represent\Property(type="integer")
     * @Represent\Group(name="private")
     */
    private $age;

    /**
     * @Represent\Hide()
     */
    public $publicTest;

    /**
     * @Represent\Embedded()
     */
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