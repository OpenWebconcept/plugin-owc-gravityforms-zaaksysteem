<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Handlers;

use OWC\Zaaksysteem\Http\Response;
use OWC\Zaaksysteem\Http\Errors\BadRequestError;

class BadRequestHandler implements HandlerInterface
{
    public function handle(Response $response): Response
    {
        if ($response->getResponseCode() !== 400) {
            return $response;
        }

        throw BadRequestError::fromResponse($response);
    }
}
