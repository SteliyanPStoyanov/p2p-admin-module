<?php

namespace Modules\Core\Exceptions;

use Modules\Core\Exceptions\BaseException;

class NotExistingMethodException extends BaseException
{
	public function __construct($class, $method)
	{
        $message = 'Method: ' . $method . ' of class: '
        	.  $this->getClassName($class) . ' is not exist';

        return parent::__construct($message);
    }
}
