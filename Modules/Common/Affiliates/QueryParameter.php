<?php

namespace Modules\Common\Affiliates;

class QueryParameter
{
    protected array $parameters;

    /**
     * QueryParameter constructor.
     * @param array $parameters
     */
    public function __construct(
        array $parameters = []
    )
    {
       $this->parameters =  $parameters;
    }

    /**
     * @param string $query
     * @return static
     */
    public static function fromString(string $query = '')
    {
        if ($query === '') {
            return new static();
        }

        return new static(
            self::mapToAssoc(
                explode('&', $query),
                function (string $keyValue) {
                    $parts = explode('=', $keyValue, 2);

                    return count($parts) === 2
                        ? [$parts[0], rawurldecode($parts[1])]
                        : [$parts[0], null];
                }
            )
        );
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $this->parameters[$key] ?? $default;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function set(string $key, string $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function unset(string $key): self
    {
        unset($this->parameters[$key]);

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $keyValuePairs = self::map(
            $this->parameters,
            fn($value, $key) => "{$key}=" . rawurlencode($value)
        );

        return implode('&', $keyValuePairs);
    }

    /**
     * @param array $items
     * @param callable $callback
     * @return array
     */
    public static function map(array $items, callable $callback): array
    {
        $keys = array_keys($items);

        $items = array_map($callback, $items, $keys);

        return array_combine($keys, $items);
    }

    /**
     * @param array $items
     * @param callable $callback
     * @return mixed
     */
    public static function mapToAssoc(array $items, callable $callback)
    {
        return array_reduce(
            $items,
            function (array $assoc, $item) use ($callback) {
                [$key, $value] = $callback($item);
                $assoc[$key] = $value;

                return $assoc;
            },
            []
        );
    }
}
