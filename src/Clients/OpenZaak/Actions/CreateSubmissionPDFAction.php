<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\OpenZaak\Actions;

use OWC\Zaaksysteem\Contracts\AbstractCreateSubmissionPDFAction;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Traits\InformationObject;

class CreateSubmissionPDFAction extends AbstractCreateSubmissionPDFAction
{
    use InformationObject;

    public const CLIENT_NAME = 'openzaak';
    public const FORM_SETTING_SUPPLIER_KEY = 'openzaak';

    public function addSubmissionPDF(): ?Zaakinformatieobject
    {
        $args = $this->getSubmissionArgsPDF();

        if (empty($args)) {
            return null;
        }

        return $this->connectZaakToSubmissionPDF($this->createSubmissionPDF($args));
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

        $args = [];
        $args['titel'] = $fileName;
        $args['formaat'] = $this->getContentType($pdfURL);
        $args['bestandsnaam'] = sprintf('%s.pdf', \sanitize_title($fileName));
        $args['bestandsomvang'] = (int) $this->getContentLength($pdfURL);
        $args['inhoud'] = $this->informationObjectToBase64($pdfURL);
        $args['vertrouwelijkheidaanduiding'] = 'vertrouwelijk';
        $args['auteur'] = 'OWC';
        $args['status'] = 'gearchiveerd';
        $args['taal'] = 'nld';
        $args['versie'] = 1;
        $args['bronorganisatie'] = ContainerResolver::make()->rsin();
        $args['creatiedatum'] = date('Y-m-d');
        $args['informatieobjecttype'] = $informationObectType;

        return $args;
    }
}
