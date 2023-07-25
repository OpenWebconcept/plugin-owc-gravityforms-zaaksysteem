<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Support;

use RuntimeException;

class ParameterBag implements \IteratorAggregate, \Countable
{
    protected array $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the parameters.
     * @param string|null $key The name of the parameter to return or null to get them all
     * @return array
     */
    public function all(/*string $key = null*/)
    {
        $key = (string) (func_num_args() > 0 ? func_get_arg(0) : '');

        if (empty($key)) {
            return $this->parameters;
        }

        if (!\is_array($value = $this->parameters[$key] ?? [])) {
            throw new RuntimeException(sprintf(
                'Unexpected value for parameter "%s": expecting "array", got "%s".',
                (string) $key,
                gettype($value)
            ));
        }

        return $value;
    }

    /**
     * Returns the parameter keys.
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->parameters);
    }

    /**
     * Replaces the current parameters by a new set.
     */
    public function replace(array $parameters = []): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Adds parameters.
     */
    public function add(array $parameters = []): void
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    /**
     * Returns a parameter by name.
     * @param mixed $default The default value if the parameter key does not exist
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return \array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }

    /**
     * Sets a parameter by name.
     * @param mixed $value The value
     */
    public function set(string $key, $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Returns true if the parameter is defined.
     * @return bool
     */
    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->parameters);
    }

    /**
     * Removes a parameter.
     */
    public function remove(string $key): void
    {
        unset($this->parameters[$key]);
    }

    /**
     * Returns the alphabetic characters of the parameter value.
     * @return string
     */
    public function getAlpha(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }

    /**
     * Returns the alphabetic characters and digits of the parameter value.
     * @return string
     */
    public function getAlnum(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }

    /**
     * Returns the digits of the parameter value.
     * @return string
     */
    public function getDigits(string $key, string $default = ''): string
    {
        // we need to remove - and + because they're allowed in the filter
        return str_replace(['-', '+'], '', $this->filter($key, $default, \FILTER_SANITIZE_NUMBER_INT));
    }

    /**
     * Returns the parameter value converted to integer.
     * @return int
     */
    public function getInt(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    /**
     * Returns the parameter value converted to boolean.
     * @return bool
     */
    public function getBoolean(string $key, bool $default = false): bool
    {
        return (bool) $this->filter($key, $default, \FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Filter key.
     * @param mixed $default Default = null
     * @param int   $filter  FILTER_* constant
     * @param mixed $options Filter options
     * @return mixed
     */
    public function filter(string $key, $default = null, int $filter = \FILTER_DEFAULT, $options = [])
    {
        $value = $this->get($key, $default);

        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!\is_array($options) && $options) {
            $options = ['flags' => $options];
        }

        // Add a convenience check for arrays.
        if (\is_array($value) && !isset($options['flags'])) {
            $options['flags'] = \FILTER_REQUIRE_ARRAY;
        }

        if ((\FILTER_CALLBACK & $filter) && !(($options['options'] ?? null) instanceof \Closure)) {
            throw new \InvalidArgumentException(sprintf(
                'A Closure must be passed to "%s()" when FILTER_CALLBACK is used, "%s" given.',
                __METHOD__,
                get_debug_type($options['options'] ?? null)
            ));
        }

        return filter_var($value, $filter, $options);
    }

    /**
     * Returns an iterator for parameters.
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->parameters);
    }

    /**
     * Returns the number of parameters.
     * @return int
     */
    public function count(): int
    {
        return \count($this->parameters);
    }
}
