<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http;

class Response
{
    protected array $headers;
    protected array $response;
    protected string $body;
    protected array $cookies;
    protected array $json;

    public function __construct(array $headers, array $response, string $body, array $cookies = [])
    {
        $this->headers = $headers;
        $this->response = $response;
        $this->body = $body;
        $this->cookies = $cookies;
        $this->json = $this->parseAsJson($this->body);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function getResponseCode(): ?int
    {
        return $this->response['code'] ?? null;
    }

    public function getResponseMessage(): ?string
    {
        return $this->response['message'] ?? null;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getParsedJson(): array
    {
        return $this->json;
    }

    protected function parseAsJson(string $body): array
    {
        $decoded = json_decode($body, true, 512);

        if (! is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }
}
