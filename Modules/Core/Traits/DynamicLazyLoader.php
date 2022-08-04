<?php

namespace Modules\Core\Traits;

use \Exception;

trait DynamicLazyLoader
{
    protected string $repositoryNamespace = 'Modules\\Admin\\Repositories\\';

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $namespace = $this->repositoryNamespace;
        if (!empty($arguments[0])) {
            $namespace = $arguments[0];
        }

        if (!isset($namespace)) {
            throw new Exception($namespace . ' property is not set.');
        }

        $property = lcfirst(str_replace('get', '', $name));

        if (!isset($this->{$property})) {
            $class = $namespace . ucfirst($property);
            if (!class_exists($class)) {
                throw new Exception($class . ' does not exists!');
            }

            $this->{$property} = \App::make($class, $arguments);
        }

        return $this->{$property};
    }
}
