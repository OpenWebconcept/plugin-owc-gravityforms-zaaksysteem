<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\DecosJoin\Actions;

use OWC\Zaaksysteem\Contracts\AbstractCreateSubmissionPDFAction;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;

class CreateSubmissionPDFAction extends AbstractCreateSubmissionPDFAction
{
    public const CLIENT_NAME = 'decos-join';
    public const FORM_SETTING_SUPPLIER_KEY = 'decos-join';

    public function addSubmissionPDF(): ?Zaakinformatieobject
    {
        $args = $this->getSubmissionArgsPDF();

        if (empty($args)) {
            return null;
        }

        try {
            return $this->connectZaakToSubmissionPDF($this->createSubmissionPDF($args));
        } catch (\Exception $e) {
            error_log('Creating submission PDF failed. Arguments: ' . json_encode($args));
            throw $e;
        }
    }
}
