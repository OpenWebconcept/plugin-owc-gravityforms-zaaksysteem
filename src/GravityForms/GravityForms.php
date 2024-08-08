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
            return $form;
        }

        try {
            $zaak = $this->createZaak($entry, $form);

            if (! $zaak instanceof Zaak) { // Fallback.
                throw new Exception('Het verwachte resultaat na het aanmaken van een Zaak voldoet niet.');
            }
        } catch(Exception $e) {
            echo view('form-submission-create-zaak-failed.php', [
                'error' => $e->getMessage(),
            ]);

            exit;
        }

        // Store the generated zaak URL in the entry's metadata for future reference after form submission.
        gform_update_meta($entry['id'], 'owc_gz_created_zaak_url', $zaak->url);

        try {
            $uploadsResult = $this->createUploadedDocuments($entry, $form, $zaak);

            if (false === $uploadsResult) { // Fallback.
                throw new Exception('Één of meerdere bestanden konden niet toegevoegd worden aan uw zaak.');
            }
        } catch(Exception $e) {
            echo view('form-submission-uploads-failed.php', [
                'error' => $e->getMessage(),
            ]);

            exit;
        }

        try {
            $pdfResult = $this->createSubmissionPDF($entry, $form, $zaak);

            if (! $pdfResult instanceof Zaakinformatieobject) { // Fallback.
                throw new Exception('Het verwachte resultaat na het toevoegen van het document met de originele aanvraag voldoet niet.');
            }
        } catch(Exception $e) {
            echo view('form-submission-pdf-failed.php', [
                'error' => $e->getMessage(),
            ]);

            exit;
        }

        return $form;
    }

    protected function createZaak(array $entry, array $form): Zaak
    {
        $action = sprintf('OWC\Zaaksysteem\Clients\%s\Actions\CreateZaakAction', $this->supplier);

        if (! class_exists($action)) {
            throw new ResourceNotFoundError(sprintf('Class "%s" does not exists. Verify if the selected supplier has the required action class', $action));
        }

        $instance = new $action();

        return $instance->addZaak($entry, $form);
    }

    /**
     * Uploads uploaded by a resident.
     * These uploads are created as information objects and are connected to the Zaak as a Zaakinformatieobject.
     */
    protected function createUploadedDocuments(array $entry, array $form, Zaak $zaak): ?bool
    {
        $action = sprintf('OWC\Zaaksysteem\Clients\%s\Actions\CreateUploadedDocumentsAction', $this->supplier);

        if (! class_exists($action)) {
            throw new ResourceNotFoundError(sprintf('Class "%s" does not exists. Verify if the selected supplier has the required action class', $action));
        }

        $instance = new $action($entry, $form, $zaak);

        return $instance->addUploadedDocuments();
    }

    /**
     * Generated PDF based on the submission used for creating a Zaak.
     * PDF is created as a information object and is connected to the Zaak as a Zaakinformatieobject.
     */
    protected function createSubmissionPDF(array $entry, array $form, Zaak $zaak): ?Zaakinformatieobject
    {
        $action = sprintf('OWC\Zaaksysteem\Clients\%s\Actions\CreateSubmissionPDFAction', $this->supplier);

        if (! class_exists($action)) {
            throw new ResourceNotFoundError(sprintf('Class "%s" does not exists. Verify if the selected supplier has the required action class', $action));
        }

        $instance = new $action($entry, $form, $zaak);

        return $instance->addSubmissionPDF();
    }
}
