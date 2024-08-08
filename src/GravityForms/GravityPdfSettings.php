<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use GPDFAPI;

class GravityPdfSettings
{
    protected array $entry;
    protected array $form;

    public function __construct(array $entry, array $form)
    {
        $this->entry = $entry;
        $this->form = $form;
    }

    /**
     * Get the first ID of the PDF settings configured per form.
     */
    public function pdfFormSettingID(): string
    {
        $pdfFormSettings = array_keys($this->getPdfFormSettings([]));

        return $pdfFormSettings[0] ?? '';
    }

    protected function getPdfFormSettings($default = null)
    {
        if (empty($this->form['gfpdf_form_settings']) || ! is_array($this->form['gfpdf_form_settings'])) {
            return $default;
        }

        return array_filter($this->form['gfpdf_form_settings'], function ($pdfFormSetting) {
            return ! empty($pdfFormSetting['active']);
        });
    }


    public function pdfFormSettingIsActive(): bool
    {
        $pdfFormSettings = $this->getPdfFormSettings();

        if (empty($pdfFormSettings)) {
            return false;
        }

        $pdfFormSettings = reset($pdfFormSettings);

        return $pdfFormSettings['active'] ?? false;
    }

    /**
     * Get the URL of the PDF that is generated after submitting the form.
     */
    public function pdfURL(): string
    {
        $pdfFormSettingID = $this->pdfFormSettingID();

        if (empty($pdfFormSettingID)) {
            return '';
        }

        $pdfModel = GPDFAPI::get_pdf_class('model');

        if (\is_wp_error($pdfModel)) {
            return '';
        }

        return $pdfModel->get_pdf_url($pdfFormSettingID, $this->entry['id']);
    }

    /**
     * This method enables and disables the 'public_access' setting.
     * By default the generated PDF's are protected.
     */
    public function updatePublicAccessSettingPDF(string $access = ''): bool
    {
        $pdfFormSettingID = $this->pdfFormSettingID();
        $settings = GPDFAPI::get_pdf($this->form['id'], $pdfFormSettingID);

        if (! is_array($settings)) {
            return false;
        }

        $settings['public_access'] = 'enable' === $access ? 'Yes' : '';

        return GPDFAPI::update_pdf($this->form['id'], $pdfFormSettingID, $settings);
    }
}
