<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Handlers;

use OWC\Zaaksysteem\Http\Response;

class ExpandRequestHandler implements HandlerInterface
{
    public function handle(Response $response): Response
    {
        $data = $response->getParsedJson();

        if (! $this->hasExpandedEntities($data)) {
            return $response;
        }

        $data = $this->mergeExpandData($data);

        $response->modify($data);

        return $response;
    }

    protected function mergeExpandData(array $data): array
    {
        if (! isset($data['_expand'])) {
            return $data;
        }

        foreach ($data['_expand'] as $name => $expandedValue) {
            // If the original entry looks like an URL, replace it with the expanded object.
            if (isset($data[$name]) && is_string($data[$name]) && strpos($data[$name], 'http') !== false) {
                $data[$name] = $expandedValue;
            }

            // If it's an array however, we'll merge it recursively.
            if (is_array($expandedValue)) {
                $data[$name] = $this->mergeExpandData($expandedValue);
            }
        }

        // Some expanded entities (which by now are merged into the main $data array)
        // contain another expanded entity. We have to recursivly merge them.
        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = $this->mergeExpandData($value);
            }
        }

        unset($data['_expand']);

        return $data;
    }

    protected function hasExpandedEntities(array $data): bool
    {
        return isset($data['_expand']) && ! empty($data['_expand']);
    }
}
