<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Handlers;

use OWC\Zaaksysteem\Http\Response;

interface HandlerInterface
{
    public function handle(Response $response): Response;
}
