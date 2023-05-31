<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http\WordPress;

use OWC\Zaaksysteem\Http\RequestClientInterface;
use OWC\Zaaksysteem\Http\RequestOptions;
use OWC\Zaaksysteem\Http\Response;

class WordPressRequestClient implements RequestClientInterface
{
    protected RequestOptions $options;

    public function __construct(?RequestOptions $options = null)
    {
        $this->options = $options ?: new RequestOptions([]);
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

    protected function handleResponse($response): Response
    {
        if (is_wp_error($response)) {
            throw WordPressRequestError::fromWpError($response);
        }

        return WordPressClientResponse::fromResponse($response);
    }

    protected function mergeRequestOptions(?RequestOptions $options = null): RequestOptions
    {
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
