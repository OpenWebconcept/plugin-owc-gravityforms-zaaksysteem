<?php

namespace OWC\Zaaksysteem\Endpoints\Filter;

use DateTimeInterface;
use OWC\Zaaksysteem\Entities\Zaaktype;

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

    public function orderBy(string $orderBy, string $orderByDirection = '-')
    {
        if (! $this->orderByParamIsValid($orderBy)) {
            return $this;
        }

        return $this->add('ordering', sprintf('%s%s', $this->sanitizeOrderByDirectionParam($orderByDirection), $orderBy));
    }

    private function orderByParamIsValid(string $orderBy): bool
    {
        /**
         * Might be used in other places, in that case
         * move to config/container.
         */
        $orderByParams = [
            'startdatum',
            'einddatum',
            'publicatiedatum',
            'archiefactiedatum',
            'registratiedatum',
            'identificatie',
        ];

        return in_array($orderBy, $orderByParams);
    }

    /**
     * Sanitizes the order direction parameter for the "order by" filter.
     *
     * Accepts only the characters '-' or '+':
     * - '+' indicates ascending order
     * - '-' indicates descending order
     */
    private function sanitizeOrderByDirectionParam(string $orderByDirection): string
    {
        return '+' === $orderByDirection ? '+' : '-';
    }

    public function byZaaktypeIdentification(Zaaktype $zaaktype)
    {
        return $this->add('identificatie', $zaaktype->identificatie);
    }

    public function byBsn(string $bsn): self
    {
        return $this->add(
            'rol__betrokkeneIdentificatie__natuurlijkPersoon__inpBsn',
            $bsn
        );
    }

    public function byKVK(string $kvk): self
    {
        return $this->add(
            'rol__betrokkeneIdentificatie__vestiging__vestigingsNummer',
            $kvk
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
