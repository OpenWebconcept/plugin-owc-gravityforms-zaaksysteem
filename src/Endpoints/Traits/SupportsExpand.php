<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints\Traits;

use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Endpoints\ZakenEndpoint;

trait SupportsExpand
{
    protected bool $expandEnabled = true;

    protected $expandSupport = [
        ZakenEndpoint::class => [ // ZakenAPI
            'zaaktype',
            'status',
            'status.statustype',
            'hoofdzaak.status.statustype',
            'hoofdzaak.deelzaken.status.statustype',
        ],
        /**
         * The next endpoints do support the expand functionality,
         * but have yet to be tested.
         */
        // EnkelvoudiginformatieobjectenEndpoint::class => [ // DocumentAPI
        //     'zaaktype',
        //     'status',
        //     'status.statustype',
        //     'hoofdzaak.status.statustype',
        //     'hoofdzaak.deelzaken.status.statustype',
        // ],
        // ObjectinformatieEndpoint::class => [
        //     'zaaktype',
        //     'status',
        //     'status.statustype',
        //     'hoofdzaak.status.statustype',
        //     'hoofdzaak.deelzaken.status.statustype',
        // ],
    ];

    public function expandIsEnabled(): bool
    {
        return $this->expandEnabled &&
            Plugin::getInstance()->getContainer()->get('expand_enabled');
    }

    public function expandAll(): static
    {
        $this->expandEnabled = true;

        return $this;
    }

    public function expandNone(): static
    {
        $this->expandEnabled = false;

        return $this;
    }

    public function expandExcept(array $resources): static
    {
        if ($this->endpointSupportsExpand() === false) {
            return $this;
        }

        $this->expandSupport[$this::class] = array_diff($this->expandSupport[$this::class], $resources);

        return $this;
    }

    public function expandOnly(array $resources): static
    {
        if ($this->endpointSupportsExpand() === false) {
            return $this;
        }

        $this->expandSupport[$this::class] = array_intersect($this->expandSupport[$this::class], $resources);

        return $this;
    }

    protected function endpointSupportsExpand(): bool
    {
        return isset($this->expandSupport[$this::class])
            && ! empty($this->expandSupport[$this::class]);
    }

    protected function getExpandableResources(): array
    {
        if ($this->endpointSupportsExpand() === false) {
            return [];
        }

        return $this->expandSupport[$this::class];
    }
}
