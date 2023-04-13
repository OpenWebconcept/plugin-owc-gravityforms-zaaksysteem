<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Support;

use OWC\Zaaksysteem\Http\PageMeta;

class PagedCollection extends Collection
{
    protected PageMeta $pageMeta;

    public function __construct(iterable $data, PageMeta $pageMeta)
    {
        parent::__construct($data);

        $this->pageMeta = $pageMeta;
    }

    public function pageMeta(): PageMeta
    {
        return $this->pageMeta;
    }
}
