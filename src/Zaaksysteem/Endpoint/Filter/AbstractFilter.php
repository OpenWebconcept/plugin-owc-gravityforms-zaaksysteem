<?php

namespace OWC\Zaaksysteem\Endpoint\Filter;

use DateTimeInterface;

abstract class AbstractFilter
{
    protected array $parameters;

    const OPERATOR_EQUALS = '=';
    const OPERATOR_IS_NULL = 'isnull';
    const OPERATOR_GT = 'gt';
    const OPERATOR_GTE = 'gte';
    const OPERATOR_LT = 'lt';
    const OPERATOR_LTE = 'lte';

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return array_filter($this->parameters, function ($param) {
            return $param !== null;
        });
    }

    public function get(string $name, $default = null)
    {
        return $this->parameters[$name] ?? $default;
    }

    public function add(string $name, $value): self
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    public function remove(string $name)
    {
        unset($this->parameters[$name]);
    }

    public function has(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    public function page(int $pageNumber): self
    {
        return $this->add('page', $pageNumber);
    }

    protected function addDateFilter(
        string $fieldName,
        DateTimeInterface $date,
        string $operator = self::OPERATOR_EQUALS,
        string $dateFormat = 'Y-m-d'
    ) {
        if (! $this->isSupportedOperator($operator)) {
            throw new \InvalidArgumentException(sprintf('Invalid operator "%s" given', $operator));
        }

        if ($operator !== self::OPERATOR_EQUALS) {
            $fieldName = $fieldName .= '__' . $this->getOperatorAppendix($operator);
        }

        return $this->add($fieldName, $date->format($dateFormat));
    }

    protected function isSupportedOperator(string $operator)
    {
        $supported = [
            self::OPERATOR_EQUALS, self::OPERATOR_IS_NULL, self::OPERATOR_GT,
            self::OPERATOR_GTE, self::OPERATOR_LT, self::OPERATOR_LTE
        ];

        return in_array($operator, $supported);
    }

    protected function getOperatorAppendix(string $operator): string
    {
        if ($operator === self::OPERATOR_EQUALS) {
            return '';
        }

        return $operator;
    }
}
