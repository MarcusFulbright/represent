<?php

namespace Represent\Annotations;

use Represent\Enum\ExclusionPolicyEnum;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class ExclusionPolicy
{
    private $policy;

    public function __construct(array $options)
    {
        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }
        }

        if (array_key_exists('policy', $options) && !in_array($options['policy'], ExclusionPolicyEnum::toArray())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'type must be one of the following values: %s',
                    implode(
                        ', ',
                        ExclusionPolicyEnum::toArray()
                    )
                )
            );
        }

        $this->policy = $options['policy'];
    }

    /**
     * @param mixed $policy
     */
    public function setPolicy($policy)
    {
        $this->policy = $policy;
    }

    /**
     * @return mixed
     */
    public function getPolicy()
    {
        return $this->policy;
    }
}