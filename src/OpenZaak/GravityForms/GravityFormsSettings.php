<?php

namespace OWC\OpenZaak\GravityForms;

class GravityFormsSettings
{
    /** @var string */
    protected $prefix = 'owc-openzaak-';

    /** @var string */
    protected $name = 'gravityformsaddon_owc-openzaak_settings';

    /** @var array */
    protected $options = [];

    final private function __construct()
    {
        $this->options = \get_option($this->name, []);
    }

    /**
     * Static constructor
     *
     * @return self
     */
    public static function make(): self
    {
        return new static();
    }

    /**
     * Get the value from the database.
     *
     * @param string $key
     * @return string
     */
    public function get(string $key): string
    {
        $key = $this->prefix . $key;
        return $this->options[$key] ?? '';
    }
}