<?php

namespace OWC\Zaaksysteem\Endpoint\Filter;

use DateTimeInterface;
use OWC\Zaaksysteem\Entities\Zaaktype;

use function Yard\DigiD\Foundation\Helpers\resolve;
use function OWC\Zaaksysteem\Foundation\Helpers\decrypt;

class ZakenFilter extends AbstractFilter
{
// identificatie
// bronorganisatie
// archiefnominatie
// archiefnominatie__in
// archiefactiedatum
// archiefactiedatum__lt
// archiefactiedatum__gt
// archiefstatus
// archiefstatus__in
// rol__betrokkeneType
// rol__betrokkene
// rol__omschrijvingGeneriek
// maximaleVertrouwelijkheidaanduiding
// rol__betrokkeneIdentificatie__natuurlijkPersoon__inpBsn
// rol__betrokkeneIdentificatie__medewerker__identificatie
// rol__betrokkeneIdentificatie__organisatorischeEenheid__identificatie
// ordering

    public function byZaaktype(Zaaktype $zaaktype)
    {
        return $this->add('zaaktype', $zaaktype->url);
    }

    public function byCurrentBsn()
    {
        $bsn = resolve('session')->getSegment('digid')->get('bsn');

        return $this->add(
            'rol__betrokkeneIdentificatie__natuurlijkPersoon__inpBsn',
            decrypt($bsn)
        );
    }

    public function byStartDate(DateTimeInterface $startDate, string $operator = self::OPERATOR_EQUALS)
    {
        return $this->addDateFilter('startdatum', $startDate, $operator, 'Y-m-d');
    }

    public function byArchiveActionDate(DateTimeInterface $endDate, string $operator = self::OPERATOR_EQUALS)
    {
        return $this->addDateFilter('archiefactiedatum', $endDate, $operator, 'Y-m-d');
    }
}
