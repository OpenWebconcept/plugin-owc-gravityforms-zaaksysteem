<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use OWC\Zaaksysteem\Entities\Enkelvoudiginformatieobject;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;
use OWC\Zaaksysteem\GravityForms\GravityPdfSettings;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Traits\CheckURL;
use OWC\Zaaksysteem\Traits\FormSetting;
use OWC\Zaaksysteem\Traits\InformationObject;

abstract class AbstractCreateSubmissionPDFAction
{
    use CheckURL;
    use FormSetting;
    use InformationObject;

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

    abstract public function addSubmissionPDF(): ?Zaakinformatieobject;

    public function getInformationObjectType(): string
    {
        return $this->informationObjectTypeFormSetting($this->form, static::FORM_SETTING_SUPPLIER_KEY);
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

    /**
     * Get the generated PDF of the submission.
     */
    protected function getSubmissionArgsPDF(): array
    {
        if (! class_exists('GPDFAPI')) {
            return [];
        }

        if (! $this->gravityPdfSettings->pdfFormSettingIsActive()) {
            return [];
        }

        $pdfURL = $this->gravityPdfSettings->pdfURL();

        if (empty($pdfURL)) {
            return [];
        }

        // Enable the public access setting so the args can be prepared.
        $this->gravityPdfSettings->updatePublicAccessSettingPDF('enable');

        if (! $this->checkURL($pdfURL)) {
            $this->gravityPdfSettings->updatePublicAccessSettingPDF();

            return [];
        }

        $args = $this->prepareFormSubmissionArgsPDF('Aanvraag - eFormulier', $pdfURL);

        // Disable the public access setting again so the PDF stays protected.
        $this->gravityPdfSettings->updatePublicAccessSettingPDF();

        return $args;
    }

    public function prepareFormSubmissionArgsPDF(string $fileName, string $pdfURL): array
    {
        $informationObectType = $this->getInformationObjectType();

        if (empty($informationObectType)) {
            return [];
        }

        $bestandsomvang = $this->getContentLength($pdfURL);
        $inhoud = $this->informationObjectToBase64($pdfURL);

        $args = [];
        $args['titel'] = $fileName;
        $args['formaat'] = $this->getContentType($pdfURL);
        $args['bestandsnaam'] = sprintf('%s.pdf', \sanitize_title($fileName));
        $args['bestandsomvang'] = $bestandsomvang ? (int) $bestandsomvang : strlen($inhoud);
        $args['inhoud'] = $inhoud;
        $args['vertrouwelijkheidaanduiding'] = 'vertrouwelijk';
        $args['auteur'] = 'OWC';
        $args['status'] = 'gearchiveerd';
        $args['taal'] = 'nld';
        $args['bronorganisatie'] = ContainerResolver::make()->rsin();
        $args['creatiedatum'] = date('Y-m-d');
        $args['informatieobjecttype'] = $informationObectType;

        return $args;
    }
}
