<?php

namespace Modules\Core\Traits;

/**
 * Class ValidationTrait
 *
 * @package Modules\Core\Traits
 */
trait ValidationTrait
{
    private $configuration;

    /**
     * @param $key
     * @param bool $default
     *
     * @return bool|mixed
     */
    public function getConfiguration($key, $default = false)
    {
        if ($this->configuration === null) {
            $this->configuration = config('validation');
        }
        $keyExplode = explode(".",$key);

        return $this->configuration[$keyExplode[0]][$keyExplode[1]] ?? $default;
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    public function areAllFieldsNull(array $array): bool
    {
        return (bool)!array_filter($array);
    }

    /**
     * @param array $data
     * @param array $keys
     *
     * @return bool
     */
    public function checkForKeysExistence(array $data, array $keys)
    {
        return count(array_intersect(array_keys($data), $keys)) === count($keys);
    }
}
