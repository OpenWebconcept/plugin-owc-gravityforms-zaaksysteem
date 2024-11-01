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
            if (is_array($expandedValue)) {
                if (!isset($data[$name]) || !is_array($data[$name])) {
                    $data[$name] = [];
                }

                $data[$name] = $this->mergeExpandData(array_merge($data[$name], $expandedValue));
            } else {
                $data[$name] = $expandedValue;
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
