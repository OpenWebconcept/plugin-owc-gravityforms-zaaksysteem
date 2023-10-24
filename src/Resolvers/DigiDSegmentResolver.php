<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Resolvers;

use Aura\Session\Segment;

use function Yard\DigiD\Foundation\Helpers\decrypt;
use function Yard\DigiD\Foundation\Helpers\resolve;

class DigiDSegmentResolver
{
    protected Segment $segment;

    final private function __construct()
    {
        $this->segment = resolve('session')->getSegment('digid');
    }

    public static function make(): self
    {
        return new static();
    }

    public function get(string $key)
    {
        return $this->segment[$key] ?? null;
    }

    public function bsn(): string
    {
        $bsn = $this->segment->get('bsn');

        return ! empty($bsn) && is_string($bsn) ? decrypt($bsn) : '';
    }
}
