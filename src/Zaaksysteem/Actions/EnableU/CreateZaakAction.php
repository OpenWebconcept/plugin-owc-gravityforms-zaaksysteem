<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Actions\EnableU;

use OWC\Zaaksysteem\Foundation\Plugin;

class CreateZaakAction
{
    /**
     * Instance of the plugin.
     */
    protected Plugin $plugin;

    /**
     * Construct the action.
     *
     * @todo add the EnableU logic.
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
}
