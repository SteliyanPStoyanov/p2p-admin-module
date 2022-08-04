<?php

namespace Modules\Core\Exceptions;

use \Exception;
use \ReflectionClass;

class BaseException extends Exception
{
	public function getClassName($object)
	{
		return (new ReflectionClass($object))->getShortName();
	}
}
