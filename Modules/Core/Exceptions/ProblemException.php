<?php

namespace Modules\Core\Exceptions;

use Illuminate\Support\Facades\App;

class ProblemException extends BaseException
{
    private const DEV_ENVIRONMENT = 'dev';

    /**
     * ProblemException constructor.
     *
     * @param $primaryMsg
     * @param $additionalMsg
     */
    public function __construct($primaryMsg, $additionalMsg = '', $code = null)
    {
        if ($this->isDevEnvironment() && !empty($additionalMsg)) {
            $primaryMsg .= ' ' . $additionalMsg;
        }

        parent::__construct($primaryMsg, $code);
    }

    /**
     * @return bool
     */
    private function isDevEnvironment()
    {
        return App::environment() === self::DEV_ENVIRONMENT;
    }
}
