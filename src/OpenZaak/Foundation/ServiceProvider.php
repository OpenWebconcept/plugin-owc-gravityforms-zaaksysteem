<?php declare(strict_types=1);

namespace OWC\OpenZaak\Foundation;

/**
 * Provider which handles the registration of the plugin.
 */
abstract class ServiceProvider
{
    /**
     * Instance of the plugin.
     */
    protected Plugin $plugin;

    /**
     * Construction of the service provider.
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Register the service provider.
     */
    abstract public function boot(): void;
}
