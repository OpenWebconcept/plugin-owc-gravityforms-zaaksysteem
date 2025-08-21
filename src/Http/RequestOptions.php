<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http;

class RequestOptions
{
    protected array $options = [
        'headers' => [],
        'body' => [],
        'cookies' => [],
    ];

    public function __construct(?array $options = [])
    {
        $this->options = $options;
    }

    public function set(string $name, $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function get(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return isset($this->options[$name]);
    }

    public function addHeader(string $name, $value): self
    {
        $this->options['headers'][$name] = $value;

        return $this;
    }

    public function getHeader(string $name, $default = null)
    {
        return $this->options['headers'][$name] ?? $default;
    }

    public function addCookie(string $name, $value): self
    {
        $this->options['cookies'][$name] = $value;

        return $this;
    }

    public function getCookie(string $name, $default = null)
    {
        return $this->options['cookies'][$name] ?? $default;
    }

    public function merge(RequestOptions $options): self
    {
        $this->options = array_merge_recursive($this->options, $options->toArray());

        return $this;
    }

    public function clone(): self
    {
        return clone $this;
    }

    public function toArray(): array
    {
        return $this->options;
    }
}
