<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Http;

class PageMeta
{
    protected int $count;
    protected ?string $nextUri;
    protected ?string $previousUri;

    protected string $pageArgumentName = 'page';

    public function __construct(int $count, ?string $nextUri, ?string $previousUri)
    {
        $this->count = $count;
        $this->nextUri = $nextUri;
        $this->previousUri = $previousUri;
    }

    public static function fromResponse(Response $response)
    {
        $data = $response->getParsedJson();

        return new self(
            $data['count'] ?? 0,
            $data['next'] ?? null,
            $data['previous'] ?? null,
        );
    }

    public function getTotalCount(): int
    {
        return $this->count;
    }

    public function getNextPage(): ?string
    {
        return $this->nextUri;
    }

    public function getNextPageNumber(): ?int
    {
        if (! $this->hasNextPage()) {
            return null;
        }

        return $this->getPageArgument($this->getNextPage());
    }

    public function getPreviousPage(): ?string
    {
        return $this->previousUri;
    }

    public function getPreviousPageNumber(): ?int
    {
        if (! $this->hasPreviousPage()) {
            return null;
        }

        return $this->getPageArgument($this->getPreviousPage());
    }

    public function hasNextPage(): bool
    {
        return $this->nextUri !== null;
    }

    public function hasPreviousPage(): bool
    {
        return $this->previousUri !== null;
    }

    protected function getPageArgument(string $url): ?int
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $arguments);

        if (isset($arguments[$this->pageArgumentName]) && is_numeric($arguments[$this->pageArgumentName])) {
            return (int) $arguments[$this->pageArgumentName];
        }

        return null;
    }
}
