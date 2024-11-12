<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\WordPress;

use OWC\Zaaksysteem\Http\Response;

class WordPressClientResponse extends Response
{
    public static function fromResponse(array $response): self
    {
        $requestUrl = '';
        if (isset($response['http_response']) && is_object($response['http_response'])) {
            $respObj = $response['http_response']->get_response_object();

            $requestUrl = $respObj->url;
        }

        return new self(
            isset($response['headers']) ? $response['headers']->getAll() : [],
            $response['response'] ?? [],
            $response['body'] ?? '',
            $response['cookies'] ?? [],
            $requestUrl
        );
    }
}
