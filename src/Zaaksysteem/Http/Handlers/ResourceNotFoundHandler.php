<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Handlers;

use OWC\Zaaksysteem\Http\Response;
use OWC\Zaaksysteem\Http\Errors\ResourceNotFoundError;

class ResourceNotFoundHandler implements HandlerInterface
{
    public function handle(Response $response): Response
    {
        if ($response->getResponseCode() !== 404) {
            return $response;
        }

        throw ResourceNotFoundError::fromResponse($response);
    }
}
