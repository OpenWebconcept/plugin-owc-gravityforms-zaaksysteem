<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Services;

use Exception;
use OWC\Zaaksysteem\Contracts\Client;
use OWC\Zaaksysteem\Endpoints\Filter\ResultaattypenFilter;

class TypeRetrievalService
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetch(string $endpoint, string $emptyMessage, int $timeout = 15): array
    {
        $page = 1;
        $types = [];
        $exceptionMessage = '';

        $this->client
            ->getRequestClient()
            ->getRequestOptions()
            ->set('timeout', $timeout);

        while ($page) {
            try {
                if ('openwave' !== $this->client->getClientNamePretty()) {
                    $result = $this->client->$endpoint()->all((new ResultaattypenFilter())->page($page));
                } else {
                    $result = $this->client->$endpoint()->all();
                }
            } catch (Exception $e) {
                $exceptionMessage = $e->getMessage();

                break;
            }

            $types = array_merge($types, $result->all());
            $page = $result->pageMeta()->getNextPageNumber();
        }

        if (empty($types)) {
            throw new Exception(trim("$emptyMessage $exceptionMessage"));
        }

        return $types;
    }
}
