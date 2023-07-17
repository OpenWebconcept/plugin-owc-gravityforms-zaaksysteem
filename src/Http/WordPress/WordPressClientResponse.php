<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\WordPress;

use OWC\Zaaksysteem\Http\Response;

class WordPressClientResponse extends Response
{
    public static function fromResponse(array $response): self
    {
        return new self(
            isset($response['headers']) ? $response['headers']->getAll() : [],
            $response['response'] ?? [],
            $response['body'] ?? '',
            $response['cookies'] ?? [],
        );
    }
}
