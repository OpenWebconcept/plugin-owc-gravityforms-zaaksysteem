<?php

namespace OWC\Zaaksysteem\Endpoint\Filter;

use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Attributes\SubjectType;

class RollenFilter extends AbstractFilter
{
    public function byZaak(Zaak $zaak)
    {
        return $this->add('zaak', $zaak->url);
    }

    public function bySubject(string $subjectUri)
    {
        return $this->add('betrokkene', $subjectUri);
    }

    public function bySubjectType(SubjectType $subjectType)
    {
        return $this->add('betrokkeneType', $subjectType->get());
    }

    public function bySubjectBsn(string $bsn)
    {
        return $this->add(
            'betrokkeneIdentificatie__natuurlijkPersoon__inpBsn',
            $bsn
        );
    }
}
