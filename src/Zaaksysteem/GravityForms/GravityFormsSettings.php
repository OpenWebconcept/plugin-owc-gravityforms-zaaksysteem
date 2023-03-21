<?php

namespace OWC\Zaaksysteem\GravityForms;

class GravityFormsSettings
{
    protected string $prefix = OZ_PLUGIN_SLUG;

    protected string $name = 'gravityformsaddon_' . OZ_PLUGIN_SLUG . '_settings';

    protected array $options = [];

    final private function __construct()
    {
        $this->options = get_option($this->name, []);
    }

    /**
     * Static constructor.
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
