<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Entity;
use OWC\Zaaksysteem\Entities\Status;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Support\Collection;
use OWC\Zaaksysteem\Support\PagedCollection;

class ZakenEndpoint extends Endpoint
{
    protected string $apiType = 'zaken';
    protected string $endpoint = 'zaken';
    protected string $entityClass = Zaak::class;

    public function all(): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint),
            $this->buildRequestOptions()
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    public function get(string $identifier): ?Zaak
    {
        $response = $this->httpClient->get(
            $this->buildUri(sprintf('%s/%s', $this->endpoint, $identifier)),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function filter(Filter\ZakenFilter $filter): PagedCollection
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint, $filter),
            $this->buildRequestOptions($filter)
        );

        return $this->getPagedCollection($this->handleResponse($response));
    }

    protected function buildEntity($data): Entity
    {
        $class = $this->entityClass;
        $zaak = new $class($data, $this->client::CALLABLE_NAME, $this->client::CLIENT_NAME);

        $statusToelichting = $zaak->status instanceof Status ? $zaak->status->statustype->statusExplanation() : '';

        $zaak->setValue('leverancier', $zaak->getClientNamePretty());
        $zaak->setValue('steps', $this->addProcessStatusses($this->getStatussenSorted($zaak), $statusToelichting));
        $zaak->setValue('status_history', $zaak->statussen);
        $zaak->setValue('information_objects', $zaak->zaakinformatieobjecten);
        $zaak->setValue('status_explanation', $statusToelichting);
        $zaak->setValue('result', $zaak->resultaat);
        $zaak->setValue('image', ContainerResolver::make()->get('zaak_image'));

        return $zaak;
    }

    protected function getStatussenSorted(Entity $zaak): Collection
    {
        $zaakType = $zaak->zaaktype;
        $statusTypen = is_object($zaakType) ? $zaakType->statustypen : null;

        return $statusTypen instanceof Collection ? $statusTypen->sortByAttribute('volgnummer') : Collection::collect([]);
    }

    protected function addProcessStatusses(Collection $statussen, string $statusToelichting): Collection
    {
        if ($statussen->isEmpty() || empty($statusToelichting)) {
            return $statussen;
        }

        $filtered = $statussen->filter(function ($status) use ($statusToelichting) {
            return strtolower($status->statusExplanation()) === strtolower($statusToelichting);
        });

        $current = $filtered->first() ? $filtered->first()->volgnummer() : null;

        if (empty($current)) {
            return $statussen;
        }

        return $statussen->map(function ($status) use ($current) {
            $volgnummer = (int) $status->volgnummer();
            $currentNum = (int) $current;

            if ($volgnummer < $currentNum) {
                $status->setValue('processStatus', 'past');
            } elseif ($volgnummer === $currentNum) {
                $status->setValue('processStatus', 'current');
            } else {
                $status->setValue('processStatus', 'future');
            }

            return $status;
        });
    }

    public function create(Zaak $model): Zaak
    {
        /**
         * @todo add field validation
         * These fields are required on a Zaak model:
         * - bronorganisatie
         * - zaaktype (URI)
         * - verantwoordelijkeOrganisatie
         * - startdatum
         * Additionally, these rules are required to pass:
         * - zaaktype != concept
         * - laatsteBetaaldatum > NOW
         */
        $response = $this->httpClient->post(
            $this->buildUri($this->endpoint),
            $model->toJson(),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }
}
