<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\EnableU;

use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Repositories\AbstractRepository;

class ZaakRepository extends AbstractRepository
{
    /**
     * Instance of the plugin.
     */
    protected Plugin $plugin;

    /**
     * Construct the repository.
     *
     * @todo add the EnableU logic.
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
}
