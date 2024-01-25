<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use Exception;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;
use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use function OWC\Zaaksysteem\Foundation\Helpers\view;
use OWC\Zaaksysteem\Http\Errors\ResourceNotFoundError;

class GravityForms
{
    protected string $supplier;

    /**
     * Get and set the selected Zaaksysteem supplier.
     */
    protected function setSupplier(array $form): void
    {
        $this->supplier = get_supplier($form);
    }

    /**
     * Handle what happens after submitting the form.
     */
    public function GFFormSubmission(array $entry, array $form)
    {
        $this->setSupplier($form);

        if (empty($this->supplier)) {
            echo view('form-submission-create-zaak-failed.php', [
                'error' => sprintf('Formulier (%s) is niet correct ingesteld om een zaak te kunnen aanmaken.', $form['title']),
            ]);

            exit;
        }

        try {
            $zaak = $this->createZaak($entry, $form);
        } catch(Exception $e) {
            $zaak = [
                'error' => $e->getMessage(),
            ];
        }

        if (! empty($zaak['error'])) {
            echo view('form-submission-create-zaak-failed.php', $zaak);

            exit;
        }

        if (! $this->createUploadedDocuments($entry, $form, $zaak)) {
            echo view('form-submission-uploads-failed.php');

            exit;
        }

        if (! $this->createSubmissionPDF($entry, $form, $zaak)) {
            echo view('form-submission-create-zaak-failed.php', [
                'error' => 'Uw zaak is succesvol aangemaakt, echter is het document met de originele aanvraag niet gegenereerd. Excuses voor het ongemak. De zaak is wel in goede orde ontvangen.',
            ]);

            exit;
        }

        return $form;
    }

    protected function createZaak(array $entry, array $form): ?Zaak
    {
        $action = sprintf('OWC\Zaaksysteem\Clients\%s\Actions\CreateZaakAction', $this->supplier);

        if (! class_exists($action)) {
            throw new ResourceNotFoundError(sprintf('Class "%s" does not exists. Verify if the selected supplier has the required action class', $action));
        }

        $instance = new $action();

        return $instance->addZaak($entry, $form);
    }

    /**
     * Generated PDF based on the submission used for creating a Zaak.
     * PDF is created as a information object and is connected to the Zaak as a Zaakinformatieobject.
     */
    protected function createSubmissionPDF(array $entry, array $form, ?Zaak $zaak): ?Zaakinformatieobject
    {
        if (! $zaak) {
            return null;
        }

        $action = sprintf('OWC\Zaaksysteem\Clients\%s\Actions\CreateSubmissionPDFAction', $this->supplier);

        if (! class_exists($action)) {
            throw new ResourceNotFoundError(sprintf('Class "%s" does not exists. Verify if the selected supplier has the required action class', $action));
        }

        $instance = new $action($entry, $form, $zaak);

        return $instance->addSubmissionPDF();
    }

    /**
     * Uploads uploaded by a resident.
     * These uploads are created as information objects and are connected to the Zaak as a Zaakinformatieobject.
     */
    protected function createUploadedDocuments(array $entry, array $form, ?Zaak $zaak): bool
    {
        if (! $zaak) {
            return false;
        }

        $action = sprintf('OWC\Zaaksysteem\Clients\%s\Actions\CreateUploadedDocumentsAction', $this->supplier);

        if (! class_exists($action)) {
            throw new ResourceNotFoundError(sprintf('Class "%s" does not exists. Verify if the selected supplier has the required action class', $action));
        }

        $instance = new $action($entry, $form, $zaak);

        return $instance->addUploadedDocuments();
    }
}
