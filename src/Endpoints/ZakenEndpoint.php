<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Entity;
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

        $zaak->setValue('leverancier', $zaak->getClientNamePretty());
        $zaak->setValue('steps', is_object($zaak->zaaktype) && $zaak->zaaktype->statustypen instanceof Collection ? $zaak->zaaktype->statustypen->sortByAttribute('volgnummer') : []);
        $zaak->setValue('status_history', $zaak->statussen);
        $zaak->setValue('information_objects', $zaak->zaakinformatieobjecten);
        $zaak->setValue('status_explanation', $zaak->status->statustoelichting ?? '');
        $zaak->setValue('result', $zaak->resultaat);
        $zaak->setValue('image', ContainerResolver::make()->get('zaak_image'));

        return $zaak;
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
