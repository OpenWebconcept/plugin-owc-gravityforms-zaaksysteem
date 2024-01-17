<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use OWC\Zaaksysteem\Entities\Enkelvoudiginformatieobject;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;
use OWC\Zaaksysteem\GravityForms\GravityPdfSettings;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;

abstract class AbstractCreateSubmissionPDFAction
{
    public const CLIENT_NAME = '';
    public const FORM_SETTING_SUPPLIER_KEY = '';

    protected array $entry;
    protected array $form;
    protected Zaak $zaak;
    protected Client $client;
    protected GravityPdfSettings $gravityPdfSettings;

    public function __construct(array $entry, array $form, Zaak $zaak)
    {
        $this->entry = $entry;
        $this->form = $form;
        $this->zaak = $zaak;
        $this->client = ContainerResolver::make()->getApiClient(static::CLIENT_NAME);
        $this->gravityPdfSettings = new GravityPdfSettings($entry, $form);
    }

    abstract protected function getSubmissionArgsPDF(): array;

    public function getInformationObjectType(): string
    {
        return $this->form[sprintf('%s-form-setting-%s-information-object-type', OWC_GZ_PLUGIN_SLUG, static::FORM_SETTING_SUPPLIER_KEY)] ?? '';
    }

    public function createSubmissionPDF(array $pdfSubmissionArgs): ?Enkelvoudiginformatieobject
    {
        if (empty($pdfSubmissionArgs)) {
            return null;
        }

        $pdf = $this->client->enkelvoudiginformatieobjecten()->create(new Enkelvoudiginformatieobject($pdfSubmissionArgs, $this->client->getClientName(), $this->client->getClientNamePretty()));
        $pdf->setValue('zaak', $this->zaak->url); // Is needed for connecting an informatie object to a zaak.

        return $pdf;
    }

    public function connectZaakToSubmissionPDF(?Enkelvoudiginformatieobject $pdf): ?Zaakinformatieobject
    {
        if (empty($pdf)) {
            return null;
        }

        return $this->client->zaakinformatieobjecten()->create(new Zaakinformatieobject($pdf->toArray(), $this->client->getClientName(), $this->client->getClientNamePretty())); // What to do when this one fails?
    }
}
