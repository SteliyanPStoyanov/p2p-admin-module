<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Redis;

class CacheService
{
    protected string $connection;

    /**
     * RedisStorage constructor.
     */
    public function __construct()
    {
        $this->setConnection();
    }

    /**
     * @param $key
     * @param $value
     * @param int $expirationSeconds
     */
    public function set($key, $value, $expirationSeconds = 600)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        Redis::connection($this->connection)->set(
            $key,
            $value,
            'EX',
            $expirationSeconds
        );
    }

    /**
     * @param $key
     * @param bool $associative
     *
     * @return mixed
     */
    public function get($key, bool $associative = false)
    {
        return json_decode(Redis::connection($this->connection)->get($key), $associative);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function remove($key)
    {
        return Redis::connection($this->connection)->command("DEL", [$key]);
    }

    /**
     * @param string $connection
     */
    public function setConnection($connection = 'cache')
    {
        $this->connection = $connection;
    }

    /**
     * @param string $pattern
     */
    public function removeByPattern(string $pattern)
    {
        $keys = Redis::connection($this->connection)->keys($pattern);

        foreach ($keys as $key) {
            $this->remove($key);
        }
    }
}
