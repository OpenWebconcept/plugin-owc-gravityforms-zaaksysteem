<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Endpoints\Traits;

use OWC\Zaaksysteem\Endpoints\ZakenEndpoint;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

trait SupportsExpand
{
    protected bool $expandEnabled = true;

    /**
     * The current resources that will be expanded
     */
    protected array $expandCurrent = [
        ZakenEndpoint::class => ['zaaktype', 'status']
    ];

    /**
     * All supported expandable resources, subdivided by endpoint and ZGW version.
     */
    protected array $expandSupport = [
        ZakenEndpoint::class => [
            '1.5.0' => [
                'zaaktype', 'status', 'status.statustype',
                'hoofdzaak.status.statustype', 'hoofdzaak.deelzaken.status.statustype'
            ],
            '1.5.1' => [
                'deelzaken',
                'deelzaken.resultaat',
                'deelzaken.resultaat.resultaattype',
                'deelzaken.rollen',
                'deelzaken.rollen.roltype',
                'deelzaken.status',
                'deelzaken.status.statustype',
                'deelzaken.zaakinformatieobjecten',
                'deelzaken.zaakobjecten',
                'deelzaken.zaaktype',
                'eigenschappen',
                'eigenschappen.eigenschap',
                'hoofdzaak',
                'hoofdzaak.resultaat',
                'hoofdzaak.resultaat.resultaattype',
                'hoofdzaak.rollen',
                'hoofdzaak.rollen.roltype',
                'hoofdzaak.status',
                'hoofdzaak.status.statustype',
                'hoofdzaak.zaakinformatieobjecten',
                'hoofdzaak.zaakobjecten',
                'hoofdzaak.zaaktype',
                'resultaat',
                'resultaat.resultaattype',
                'rollen',
                'rollen.roltype',
                'status',
                'status.statustype',
                'zaakinformatieobjecten',
                'zaakobjecten',
                'zaaktype',
            ],
        ]
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
            ContainerResolver::make()->get('expand_enabled');
    }

    public function expandAll(): self
    {
        $this->expandEnabled = true;

        $expandVersion = ContainerResolver::make()->get('expand_version');

        $this->expandCurrent[get_class($this)] = $this->expandSupport[get_class($this)][$expandVersion];

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

        $expandVersion = ContainerResolver::make()->get('expand_version');

        $this->expandCurrent[get_class($this)] = array_diff(
            $this->expandSupport[get_class($this)][$expandVersion],
            $resources
        );

        return $this;
    }

    public function expandOnly(array $resources): self
    {
        if ($this->endpointSupportsExpand() === false) {
            return $this;
        }

        $expandVersion = ContainerResolver::make()->get('expand_version');

        $this->expandCurrent[get_class($this)] = array_intersect(
            $this->expandSupport[get_class($this)][$expandVersion],
            $resources
        );

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

        return $this->expandCurrent[get_class($this)];
    }
}
