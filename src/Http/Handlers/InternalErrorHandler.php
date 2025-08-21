<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Handlers;

use OWC\Zaaksysteem\Http\Errors\ServerError;
use OWC\Zaaksysteem\Http\Response;

class InternalErrorHandler implements HandlerInterface
{
    public function handle(Response $response): Response
    {
        if ($response->getResponseCode() !== 500) {
            return $response;
        }

        throw ServerError::fromResponse($response);
    }
}
