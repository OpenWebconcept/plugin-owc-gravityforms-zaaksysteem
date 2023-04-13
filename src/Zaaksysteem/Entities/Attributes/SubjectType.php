<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Entities\Attributes;

class SubjectType extends EnumAttribute
{
    public const VALID_MEMBERS = [
        'natuurlijk_persoon', 'niet_natuurlijk_persoon', 'vestiging',
        'organisatorische_eenheid', 'medewerker'
    ];

    protected string $name = 'subject type';
}
