<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\Mozart\Actions;

use OWC\Zaaksysteem\Contracts\AbstractCreateSubmissionPDFAction;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;

class CreateSubmissionPDFAction extends AbstractCreateSubmissionPDFAction
{
    public const CLIENT_NAME = 'mozart';
    public const FORM_SETTING_SUPPLIER_KEY = 'mozart';

    public function addSubmissionPDF(): ?Zaakinformatieobject
    {
        $args = $this->getSubmissionArgsPDF();

        if (empty($args)) {
            return null;
        }

        return $this->connectZaakToSubmissionPDF($this->createSubmissionPDF($args));
    }
}
