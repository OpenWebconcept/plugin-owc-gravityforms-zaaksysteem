<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\EnableU\Classes;

use \GPDFAPI;
use OWC\Zaaksysteem\Traits\InformationObject;

class PDFHelper
{
    use InformationObject;
    
    /**
     * Get the first ID of the PDF settings configured per form.
     * Maybe refactor this code to fetch the setting by the setting label.
     */
    public function pdfFormSettingID(array $entry, array $form): string
    {
        if (empty($form['gfpdf_form_settings']) || ! is_array($form['gfpdf_form_settings'])) {
            return '';
        }

        $pdfFormSettings = array_keys($form['gfpdf_form_settings']);

        return $pdfFormSettings[0] ?? '';
    }

    /**
     * Get the URL of the PDF that is generated after submitting the form.
     */
    public function pdfURL(array $entry, string $pdfFormSettingID): string
    {
        if (empty($pdfFormSettingID)) {
            return '';
        }
        
        $pdfModel = GPDFAPI::get_pdf_class('model');

        if (\is_wp_error($pdfModel)) {
            return '';
        }
        
        return $pdfModel->get_pdf_url($pdfFormSettingID, $entry['id']);
    }

    public function createFileName(array $zaak, array $form): string
    {
        $zaakUUID = $zaak['uuid'] ?? 'Z-23-151662'; // Default needs to be removed later.

        return \sanitize_title(sprintf('%s-%s-form-%d', $zaakUUID, strtolower($form['title']), $form['id']));
    }

    /**
     * This method enables and disables the 'public_access' setting.
     * By default the generated PDF's are protected.
     */
    public function updatePublicAccessSettingPDF(array $form, string $pdfFormSettingID, string $access = ''): bool
    {
        $settings = GPDFAPI::get_pdf($form['id'], $pdfFormSettingID);

        if(! is_array($settings)) {
            return false;
        }

        $settings['public_access'] = $access === 'enable' ? 'Yes' : '';

        return GPDFAPI::update_pdf($form['id'], $pdfFormSettingID, $settings);
    }

    public function prepareFormSubmissionArgsPDF(string $fileName, string $pdfURL): array
    {
        $args = [];
        $args['titel'] = $fileName;
        $args['formaat'] = $this->getContentType($pdfURL);
        $args['bestandsnaam'] = sprintf('%s.pdf', $fileName);
        $args['bestandsomvang'] = (int) $this->getContentLength($pdfURL);
        $args['inhoud'] = $this->informationObjectToBase64($pdfURL);
        $args['vertrouwelijkheidaanduiding'] = 'vertrouwelijk';
        $args['auteur'] = 'Yard';
        $args['taal'] = 'dut';
        $args['versie'] = 1;
        $args['informatieobjecttype'] = 'https://digikoppeling-test.gemeentehw.nl/opentunnel/00000001825766096000/openzaak/zaakdms/catalogi/api/v1/informatieobjecttypen/3beec26e-e43f-4fd2-ba09-94d47316d877';
            
        return $args;
    }
}
