<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\Procura\Actions;

use OWC\Zaaksysteem\Contracts\AbstractCreateSubmissionPDFAction;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;

class CreateSubmissionPDFAction extends AbstractCreateSubmissionPDFAction
{
    public const CLIENT_NAME = 'procura';
    public const FORM_SETTING_SUPPLIER_KEY = 'procura';

    public function addSubmissionPDF(): ?Zaakinformatieobject
    {
        $args = $this->getSubmissionArgsPDF();

        if (empty($args)) {
            return null;
        }

        return $this->connectZaakToSubmissionPDF($this->createSubmissionPDF($args));
    }
}
