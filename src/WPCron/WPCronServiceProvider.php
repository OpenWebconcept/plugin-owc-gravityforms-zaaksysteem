<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\WPCron;

use DateTime;
use DateTimeZone;
use OWC\Zaaksysteem\Foundation\ServiceProvider;
use OWC\Zaaksysteem\WPCron\Events\GravityFormsFormSettings;
use OWC\Zaaksysteem\WPCron\Events\PendingPaymentEntries;

class WPCronServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerHooks();
        $this->registerEvents();
    }

    protected function registerHooks(): void
    {
        add_action('owc_gz_form_settings_cron', [new GravityFormsFormSettings, 'init']);
        add_action('owc_gz_pending_payment_entries_cron', [new PendingPaymentEntries, 'init']);
    }

    protected function registerEvents(): void
    {
        if (! wp_next_scheduled('owc_gz_form_settings_cron')) {
            wp_schedule_event($this->timeToExecute('tomorrow 04:00:00'), 'daily', 'owc_gz_form_settings_cron');
        }

        if (! wp_next_scheduled('owc_gz_pending_payment_entries_cron')) {
            wp_schedule_event($this->timeToExecute('now'), 'hourly', 'owc_gz_pending_payment_entries_cron');
        }
    }

    protected function timeToExecute(string $datetime = 'now'): int
    {
        $currentDateTime = new DateTime($datetime, new DateTimeZone(wp_timezone_string()));

        return $currentDateTime->getTimestamp();
    }
}
