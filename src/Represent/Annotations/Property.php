<?php

namespace Represent\Annotations;

use Represent\Enum\PropertyTypeEnum;

/**
 * Used to control the serialized name and data type
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    public function __construct($options)
    {
        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }
        }

        if (array_key_exists('type', $options) && !in_array($options['type'], PropertyTypeEnum::ToArray())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'type must be one of the following values: %s',
                    implode(
                        ', ',
                        PropertyTypeEnum::toArray()
                    )
                )
            );
        }

        if (array_key_exists('name', $options)) {
            $this->name = $options['name'];
        }

        if (array_key_exists('type', $options)) {
            $this->type = $options['type'];
        }
    }

    /**
     * @param string $propertyName
     */
    public function setName($propertyName)
    {
        $this->name = $propertyName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}