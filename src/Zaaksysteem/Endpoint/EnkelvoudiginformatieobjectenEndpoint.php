<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoint;

use OWC\Zaaksysteem\Entities\Enkelvoudiginformatieobject;

class EnkelvoudiginformatieobjectenEndpoint extends Endpoint
{
    protected string $apiType = 'documenten';
    protected string $endpoint = 'enkelvoudiginformatieobjecten';
    protected string $entityClass = Enkelvoudiginformatieobject::class;

    public function get(string $identifier): ?Enkelvoudiginformatieobject
    {
        $response = $this->client->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    public function download(string $identifier)
    {
        $response = $this->client->get(
            $this->buildUri($this->endpoint . '/' . $identifier . '/download'),
            $this->buildRequestOptions()
        );

        var_dump($response);
        exit();
        //
    }
}
