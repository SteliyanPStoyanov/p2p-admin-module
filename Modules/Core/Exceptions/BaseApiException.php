<?php

namespace Modules\Core\Exceptions;

use Illuminate\Contracts\Support\Jsonable;

abstract class BaseApiException extends \Exception
{
    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->code;
    }
}
