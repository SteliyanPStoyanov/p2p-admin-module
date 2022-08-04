<?php

namespace Modules\Core\Exceptions;

use Modules\Core\Exceptions\BaseException;

class NotExistingClassException extends BaseException
{
	public function __construct($class, $envokeClass = null, $line = null)
	{
        $message = 'Class: '
        	.  $this->getClassName($class) . ' is not exist'
        	. (!empty($envokeClass) ? ' in: ' . get_class($envokeClass) : '')
        	. (!empty($line) ? ' on line: ' . $line : '')
        ;

        return parent::__construct($message);
    }
}
