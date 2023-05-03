<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\DecosJoin;

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
     * @todo add the DecosJoin logic.
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
}
