<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints\Traits;

use OWC\Zaaksysteem\Endpoints\ZakenEndpoint;
use OWC\Zaaksysteem\Foundation\Plugin;

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

    public function expandAll(): self
    {
        $this->expandEnabled = true;

        return $this;
    }

    public function expandNone(): self
    {
        $this->expandEnabled = false;

        return $this;
    }

    public function expandExcept(array $resources): self
    {
        if ($this->endpointSupportsExpand() === false) {
            return $this;
        }

        $this->expandSupport[get_class($this)] = array_diff($this->expandSupport[get_class($this)], $resources);

        return $this;
    }

    public function expandOnly(array $resources): self
    {
        if ($this->endpointSupportsExpand() === false) {
            return $this;
        }

        $this->expandSupport[get_class($this)] = array_intersect($this->expandSupport[get_class($this)], $resources);

        return $this;
    }

    protected function endpointSupportsExpand(): bool
    {
        return isset($this->expandSupport[get_class($this)])
            && ! empty($this->expandSupport[get_class($this)]);
    }

    protected function getExpandableResources(): array
    {
        if ($this->endpointSupportsExpand() === false) {
            return [];
        }

        return $this->expandSupport[get_class($this)];
    }
}
