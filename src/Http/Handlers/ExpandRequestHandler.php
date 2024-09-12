<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\Handlers;

use OWC\Zaaksysteem\Http\Response;

class ExpandRequestHandler implements HandlerInterface
{
    public function handle(Response $response): Response
    {
        $json = $response->getParsedJson();

        if (! $this->hasExpandedEntities($json)) {
            return $response;
        }

        foreach ($json['_expand'] as $type => $expandedEntity) {
            $json[$type] = $expandedEntity;
        }

        unset($json['_expand']);

        $response->modify($json);

        return $response;
    }

    protected function hasExpandedEntities(array $json): bool
    {
        return isset($json['_expand']) && !empty($json['_expand']);
    }
}
