<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

use OWC\Zaaksysteem\Entities\Statustype;
use OWC\Zaaksysteem\Entities\Zaak;

class StatussenFilter extends AbstractFilter
{
    public function byStatusType(Statustype $type)
    {
        return $this->add('statustype', $type->url);
    }

    public function byZaak(Zaak $zaak)
    {
        return $this->add('zaak', $zaak->url);
    }
}
