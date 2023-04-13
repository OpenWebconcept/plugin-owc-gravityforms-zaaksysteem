<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Attributes;

class Confidentiality extends EnumAttribute
{
    public const VALID_MEMBERS = [
        'openbaar', 'beperkt_openbaar', 'intern', 'zaakvertrouwelijk',
        'vertrouwelijk', 'confidentieel', 'geheim', 'zeer_geheim',
    ];

    protected string $name = 'confidentiality level';
}
