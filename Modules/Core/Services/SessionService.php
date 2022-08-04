<?php

namespace Modules\Core\Services;

class SessionService
{
    /**
     * @param string $key
     * @param mixed $data
     *
     * @return bool
     */
    public function add(string $key, $data)
    {
        session()->put($key, $data);

        return true;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return session($key, null);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function remove(string $key)
    {
        session()->forget($key);

        return true;
    }
}
