<?php

namespace Modules\Core\Traits;

trait StringFormatterTrait
{
    /**
     * Camel case to normal text
     *
     * @param string $text
     *
     * @return string
     */
    public function fmtCamelCaseToNormal(string $text): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $text));
    }

    /**
     * Camel case to snake case
     *
     * @param string $text
     *
     * @return string
     */
    public function fmtCamelCaseToSnakeCase(string $text): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $text));
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function fmtSnakeCaseToCamelCase(string $text): string
    {
        return str_replace('-', '', ucwords($text, '-'));
    }
}
