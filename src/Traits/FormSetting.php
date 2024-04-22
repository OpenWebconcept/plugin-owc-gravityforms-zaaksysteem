<?php

namespace OWC\Zaaksysteem\Traits;

trait FormSetting
{
    /**
     * Check if form setting is selected or configured manually.
     * Returns the selected zaaktype identifier.
     */
    public function zaaktypeIdentifierFormSetting(array $form, string $supplier): string
    {
        if ('1' === ($form[sprintf('%s-form-setting-supplier-manually', OWC_GZ_PLUGIN_SLUG)] ?? '0')) {
            $zaaktypeIdentifier = $form[sprintf('%s-form-setting-%s-identifier-manual', OWC_GZ_PLUGIN_SLUG, $supplier)] ?? null;
        } else {
            $zaaktypeIdentifier = $form[sprintf('%s-form-setting-%s-identifier', OWC_GZ_PLUGIN_SLUG, $supplier)] ?? null;
        }

        return ! empty($zaaktypeIdentifier) ? $zaaktypeIdentifier : '';
    }

    /**
     * Check if form setting is selected or configured manually.
     * Returns the selected information object type identifier.
     */
    public function informationObjectTypeFormSetting(array $form, string $supplier): string
    {
        if ('1' === ($form[sprintf('%s-form-setting-supplier-manually', OWC_GZ_PLUGIN_SLUG)] ?? '0')) {
            $zaaktypeIdentifier = $form[sprintf('%s-form-setting-%s-information-object-type-manual', OWC_GZ_PLUGIN_SLUG, $supplier)] ?? null;
        } else {
            $zaaktypeIdentifier = $form[sprintf('%s-form-setting-%s-information-object-type', OWC_GZ_PLUGIN_SLUG, $supplier)] ?? null;
        }

        return ! empty($zaaktypeIdentifier) ? $zaaktypeIdentifier : '';
    }
}
