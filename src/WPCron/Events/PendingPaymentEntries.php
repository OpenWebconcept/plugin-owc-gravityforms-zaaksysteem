<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\WPCron\Events;

use DateTime;
use DateTimeZone;
use GFAPI;
use OWC\Zaaksysteem\GravityForms\Controllers\GravityFormsPaymentController;
use OWC\Zaaksysteem\Traits\EntryNote;

class PendingPaymentEntries
{
    use EntryNote;

    /**
     * Initializes the process by retrieving entries with pending payments
     * and handling them accordingly.
     */
    public function init(): void
    {
        $entries = $this->getPendingPaymentEntries();

        if (empty($entries)) {
            return;
        }

        $this->processPendingPaymentEntries($entries);
    }

    /**
     * Retrieves entries that have a pending payment status and an associated appointment ID.
     */
    private function getPendingPaymentEntries(): array
    {
        $args = [
            'status' => 'active',
            'field_filters' => [
                [
                    'key' => 'owc_gz_created_zaak_url',
                    'value' => '',
                    'operator' => '!=',
                ],
                [
                    'key' => 'payment_status',
                    'value' => 'Pending',
                ],
            ],
        ];

        $entries = GFAPI::get_entries(0, $args);

        return is_array($entries) ? $entries : [];
    }

    /**
     * Processes each entry with a pending payment status.
     */
    private function processPendingPaymentEntries(array $entries): void
    {
        foreach ($entries as $entry) {
            $this->evaluatePendingPaymentEntry($entry);
        }
    }

    /**
     * Evaluates a single entry to determine if the payment has been pending for too long.
     * If so, triggers the appropriate action (e.g., delete the zaak).
     */
    private function evaluatePendingPaymentEntry(array $entry): void
    {
        if (! $this->hasPaymentBeenPendingTooLong($entry['id'])) {
            return;
        }

        (new GravityFormsPaymentController)->postPaymentPendingTooLong($entry, [
            'payment_status' => $entry['payment_status'],
        ]);
    }

    /**
     * Checks if the payment for an entry has been pending for too long.
     */
    private function hasPaymentBeenPendingTooLong($entryId): bool
    {
        $entry = GFAPI::get_entry($entryId);

        if ('Pending' !== $entry['payment_status']) {
            return false;
        }

        $paymentDate = $entry['payment_date'];

        if (empty($paymentDate)) {
            return false;
        }

        $paymentDateTime = (new DateTime($paymentDate))->setTimezone(new DateTimeZone(wp_timezone_string()));
        $currentDateTime = new DateTime('now', new DateTimeZone(wp_timezone_string()));

        $timeDifference = $paymentDateTime->diff($currentDateTime);

        return 1 <= $timeDifference->h || 1 <= $timeDifference->days;
    }
}
