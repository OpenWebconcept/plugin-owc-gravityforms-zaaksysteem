<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Handlers;

use OWC\Zaaksysteem\Http\Response;
use OWC\Zaaksysteem\Http\Errors\UnauthenticatedError;

class UnauthenticatedHandler implements HandlerInterface
{
    public function handle(Response $response): Response
    {
        if (! in_array($response->getResponseCode(), [401, 403])) {
            return $response;
        }

        throw UnauthenticatedError::fromResponse($response);
    }
}
