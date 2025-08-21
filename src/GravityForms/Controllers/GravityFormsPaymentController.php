<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms\Controllers;

use Exception;
use GFAPI;
use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use OWC\Zaaksysteem\Http\Errors\ResourceNotFoundError;
use OWC\Zaaksysteem\Traits\EntryNote;

class GravityFormsPaymentController
{
    use EntryNote;

    public const SUCCESSFUL_PAYMENT_STATUS = 'Paid';
    public const PENDING_PAYMENT_STATUS = 'Pending';
    public const FAILED_PAYMENT_STATUSES = [
        'Failed',
        'Refunded',
        'Voided',
        'Cancelled',
    ];

    protected string $supplier;

    public function postPaymentPendingTooLong(array $entry, array $action): void
    {
        if (self::PENDING_PAYMENT_STATUS !== $action['payment_status']) {
            return;
        }

        $this->handleZaakDeletion($entry);
    }

    /**
     * Is called after a payment status is added or changed.
     */
    public function postPaymentFailed(array $entry, array $action): void
    {
        if (! in_array($action['payment_status'], self::FAILED_PAYMENT_STATUSES)) {
            return;
        }

        $this->handleZaakDeletion($entry);
    }

    protected function handleZaakDeletion(array $entry)
    {
        $zaakURL = $this->getCreatedZaakUrl($entry);

        if (empty($zaakURL)) {
            return;
        }

        $form = GFAPI::get_form($entry['form_id']);

        $this->setSupplier($form);

        if (empty($this->supplier)) {
            return;
        }

        try {
            $this->deleteZaak($zaakURL, $form, $entry);
        } catch (Exception $e) {
            return;
        }

        $this->entryAddNote($entry['id'], __('Aangemaakte zaak geannuleerd, betaling mislukt.', 'owc-gravityforms-zaaksysteem'));
        gform_delete_meta($entry['id'], 'owc_gz_created_zaak_url');
    }

    protected function getCreatedZaakUrl(array $entry): string
    {
        $createZaakURL = gform_get_meta($entry['id'], 'owc_gz_created_zaak_url');

        return ! empty($createZaakURL) && is_string($createZaakURL) ? $createZaakURL : '';
    }

    /**
     * Get and set the selected Zaaksysteem supplier.
     */
    protected function setSupplier(array $form): void
    {
        $this->supplier = get_supplier($form);
    }

    protected function deleteZaak(string $zaakURL, array $form, array $entry): void
    {
        $action = sprintf('OWC\Zaaksysteem\Clients\%s\Actions\DeleteZaakAction', $this->supplier);

        if (! class_exists($action)) {
            throw new ResourceNotFoundError(sprintf('Class "%s" does not exists. Verify if the selected supplier has the required action class', $action));
        }

        $instance = new $action();

        $instance->deleteZaak($zaakURL, $entry, $form);
    }

    public function postPaymentCompleted(array $entry, array $action): void
    {
        if (self::SUCCESSFUL_PAYMENT_STATUS !== $action['payment_status']) {
            return;
        }

        $this->entryAddNote($entry['id'], __('Zaak definitief, betaling succesvol.', 'owc-gravityforms-zaaksysteem'), 'success');
    }
}
