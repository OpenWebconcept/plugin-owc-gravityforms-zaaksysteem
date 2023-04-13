<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Errors;

use OWC\Zaaksysteem\Http\Response;
use OWC\Zaaksysteem\Http\RequestError;

class BadRequestError extends RequestError
{
    protected array $invalidParameters = [];

    public static function fromResponse(Response $response)
    {
        $error = parent::fromResponse($response);
        if ($error->code === 0) {
            // Unhandable error.
            return $error;
        }

        $json = $response->getParsedJson();

        $error->setInvalidParameters($json['invalidParams'] ?? []);

        return $error;
    }

    public function setInvalidParameters(array $parameters)
    {
        $this->invalidParameters = $parameters;

        return $this;
    }

    public function getInvalidParameters(): array
    {
        return $this->invalidParameters;
    }

    public function hasInvalidParameters(): bool
    {
        return ! empty($this->invalidParameters);
    }
}
