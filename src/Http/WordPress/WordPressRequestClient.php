<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\WordPress;

use OWC\Zaaksysteem\Http\RequestClientInterface;
use OWC\Zaaksysteem\Http\RequestOptions;
use OWC\Zaaksysteem\Http\Response;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

class WordPressRequestClient implements RequestClientInterface
{
    protected RequestOptions $options;
    protected ContainerResolver $container;

    public function __construct(?RequestOptions $options = null)
    {
        $this->options = $options ?: new RequestOptions([]);
        $this->container = ContainerResolver::make();
    }

    /**
     * Applies SSL client certificates (public + private) on outbound cURL requests.
     *
     * Supports two configuration modes:
     *  1. New configuration where certificates are stored directly in the container
     *     (public_ssl_certificate / private_ssl_certificate)
     *  2. Legacy DigiD plugin configuration where certificates are read via:
     *        config('digid.certificate.public') / config('digid.certificate.private')
     */
    public function applyCurlSslCertificates(): self
    {
        $sslPublicCert = (string) ($this->container->get('public_ssl_certificate') ?: '');
        $sslPrivateCert = (string) ($this->container->get('private_ssl_certificate') ?: '');

        if ($this->shouldUseDigiDCertificates($sslPublicCert, $sslPrivateCert)) {
            $sslPublicCert = (string) (\Yard\DigiD\Foundation\Helpers\config('digid.certificate.public') ?: '');
            $sslPrivateCert = (string) (\Yard\DigiD\Foundation\Helpers\config('digid.certificate.private') ?: '');
        }

        if (! file_exists($sslPublicCert) || ! file_exists($sslPrivateCert)) {
            return $this;
        }

        add_action('http_api_curl', function ($handle) use ($sslPublicCert, $sslPrivateCert) {
            curl_setopt($handle, CURLOPT_SSLCERT, $sslPublicCert);
            curl_setopt($handle, CURLOPT_SSLKEY, $sslPrivateCert);
        });

        return $this;
    }

    private function shouldUseDigiDCertificates(string $sslPublicCert, string $sslPrivateCert): bool
    {
        return (! file_exists($sslPublicCert) || ! file_exists($sslPrivateCert)) && function_exists('\\Yard\\DigiD\\Foundation\\Helpers\\config');
    }

    public function setRequestOptions(RequestOptions $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getRequestOptions(): RequestOptions
    {
        return $this->options;
    }

    public function get(string $uri, ?RequestOptions $options = null): Response
    {
        $response = wp_remote_get(
            $this->buildUri($uri),
            $this->mergeRequestOptions($options)->toArray()
        );

        return $this->handleResponse($response);
    }

    public function post(string $uri, $body, ?RequestOptions $options = null): Response
    {
        $options = $this->mergeRequestOptions($options)->set('body', $body);
        $response = wp_remote_post($this->buildUri($uri), $options->toArray());

        return $this->handleResponse($response);
    }

    public function delete(string $uri, ?RequestOptions $options = null): Response
    {
        $options->set('method', 'DELETE');

        $response = wp_remote_request(
            $this->buildUri($uri),
            $this->mergeRequestOptions($options)->toArray()
        );

        return $this->handleResponse($response);
    }

    protected function handleResponse($response): Response
    {
        if (is_wp_error($response)) {
            throw WordPressRequestError::fromWpError($response);
        }

        return WordPressClientResponse::fromResponse($response);
    }

    protected function mergeRequestOptions(?RequestOptions $options = null): RequestOptions
    {
        $this->options->addHeader('_owc_request_logging', microtime(true));

        if (! $options) {
            return $this->options;
        }

        return $this->options->clone()->merge($options);
    }

    protected function buildUri($uri): string
    {
        if ($this->options->has('base_uri')) {
            $uri = rtrim($this->options->get('base_uri'), '/') . '/' . $uri;
        }

        return $uri;
    }
}
