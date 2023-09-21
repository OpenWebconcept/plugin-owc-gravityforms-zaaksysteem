<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints;

use OWC\Zaaksysteem\Entities\Enkelvoudiginformatieobject;

class EnkelvoudiginformatieobjectenEndpoint extends Endpoint
{
    protected string $endpoint = 'enkelvoudiginformatieobjecten';
    protected string $entityClass = Enkelvoudiginformatieobject::class;

    public function get(string $identifier): ?Enkelvoudiginformatieobject
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint . '/' . $identifier),
            $this->buildRequestOptions()
        );

        return $this->getSingleEntity($this->handleResponse($response));
    }

    /**
     * @todo Return a 'stream' of sorts so a controller can decide
     * what to do with the binary data of a document.
     */
    public function download(string $identifier)
    {
        $response = $this->httpClient->get(
            $this->buildUri($this->endpoint . '/' . $identifier . '/download'),
            $this->buildRequestOptions()
        );

        var_dump($response);
        exit();
        //
    }
}
