<?php

declare(strict_types=1);

namespace OWC\OpenZaak\GravityForms;

class GravityFormsSettings
{
    protected string $prefix = 'owc-openzaak-';
    protected string $name = 'gravityformsaddon_owc-gravityforms-openzaak_settings';
    protected array $options = [];

    final private function __construct()
    {
        $this->options = \get_option($this->name, []);
    }

    /**
     * Static constructor
     */
    public static function make(): self
    {
        return new static();
    }

    /**
     * Get the value from the database.
     */
    public function get(string $key): string
    {
        $key = $this->prefix . $key;
        return $this->options[$key] ?? '';
    }
}
