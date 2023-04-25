<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\EnableU;

use OWC\Zaaksysteem\Traits\InformationObject;

class CreateZaakRepository extends BaseRepository
{
    use InformationObject;

    protected string $zakenURI = 'zaken/api/v1/zaken';
    protected string $informationObjectsURI = 'documenten/api/v1/enkelvoudiginformatieobjecten';
    protected string $zaakConnectioninformationObject = 'documenten/api/v1/zaakinformatieobjecten';

    public function __construct()
    {
        parent::__construct();
    }

    public function createOpenZaak(array $args = []): array
    {
        if (! empty($args['informatieobject'])) {
            unset($args['informatieobject']);
        }
        
        return $this->request($this->makeURL($this->zakenURI), 'POST', $args);
    }

    public function addFormSubmissionPDF(array $zaak, array $entry, array $form, $args): array
    {
        $pdfFormSettingID = $this->pdfFormSettingID($entry, $form);

        if (empty($pdfFormSettingID)) {
            return [];
        }
        
        $pdfURL = $this->pdfURL($entry, $pdfFormSettingID);

        if (empty($pdfURL)) {
            return [];
        }

        // Enable the public access setting so the args can be prepared.
        $this->updatePublicAccessPDF($form, $pdfFormSettingID, 'enable');

        $args = $this->getPreservedInformationObjectArgs($args);
        $newArgs = $this->prepareFormSubmissionArgsPDF($this->createFileName($zaak, $form), $pdfURL);

        $args = array_merge($args, $newArgs);
        unset($args['informatieobject']);
        
        // Disable the public access setting again so the PDF stays protected.
        $this->updatePublicAccessPDF($form, $pdfFormSettingID);

        $informationObjectResult = $this->request($this->makeURL($this->informationObjectsURI), 'POST', $args);

        return $this->connectZaakToInformationObject($zaak, $informationObjectResult);
    }

    protected function prepareFormSubmissionArgsPDF(string $fileName, string $pdfURL): array
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

    /**
     * Get the first ID of the PDF settings configured per form.
     * Maybe refactor this code to fetch the setting by the setting label.
     */
    protected function pdfFormSettingID(array $entry, array $form): string
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
    protected function pdfURL(array $entry, string $pdfFormSettingID): string
    {
        if (empty($pdfFormSettingID)) {
            return '';
        }
        
        $pdfModel = \GPDFAPI::get_pdf_class('model');

        if (\is_wp_error($pdfModel)) {
            return '';
        }
        
        return $pdfModel->get_pdf_url($pdfFormSettingID, $entry['id']);
    }

    protected function createFileName(array $zaak, array $form): string
    {
        $zaakUUID = $zaak['uuid'] ?? 'Z-23-151662';

        return \sanitize_title(sprintf('%s-%s-form-%d', $zaakUUID, strtolower($form['title']), $form['id']));
    }

    /**
     * This method enables and disables the 'public_access' setting.
     * By default the generated PDF's are protected.
     */
    protected function updatePublicAccessPDF(array $form, string $pdfFormSettingID, string $access = ''): bool
    {
        $settings = \GPDFAPI::get_pdf($form['id'], $pdfFormSettingID);

        if(! is_array($settings)) {
            return false;
        }

        $settings['public_access'] = $access === 'enable' ? 'Yes' : '';

        return \GPDFAPI::update_pdf($form['id'], $pdfFormSettingID, $settings);
    }

    public function addInformationObjectToZaak(array $args = []): array
    {
        $args = $this->prepareInformationObjectArgs($args);

        return $this->request($this->makeURL($this->informationObjectsURI), 'POST', $args);
    }

    protected function prepareInformationObjectArgs(array $args)
    {
        $args = $this->getPreservedInformationObjectArgs($args);
        $args = $this->handleInformationObjectArgs($args);

        return $args;
    }

    /**
     * Preserve some of the args which were used to create a 'Zaak'
     */
    protected function getPreservedInformationObjectArgs(array $args): array
    {
        $preparedArgs = [];

        $keysToPreserve = [
            'bronorganisatie',
            'registratiedatum',
            'informatieobject'
        ];

        foreach ($args as $key => $arg) {
            if (! in_array($key, $keysToPreserve)) {
                continue;
            }

            if ($key === 'registratiedatum') {
                $key = 'creatiedatum';
            }

            $preparedArgs[$key] = $arg;
        }

        return $preparedArgs;
    }

    protected function handleInformationObjectArgs(array $args): array
    {
        $object = $args['informatieobject'];
        unset($args['informatieobject']);

        $args['titel'] = $this->getInformationObjectTitle($object);
        $args['formaat'] = $this->getContentType($object);
        $args['bestandsnaam'] = $this->getInformationObjectTitle($object);
        $args['bestandsomvang'] = (int) $this->getContentLength($object);
        $args['inhoud'] = $this->informationObjectToBase64($object);
        $args['vertrouwelijkheidaanduiding'] = 'vertrouwelijk';
        $args['auteur'] = 'Yard';
        $args['taal'] = 'dut';
        $args['versie'] = 1;
        $args['informatieobjecttype'] = 'https://digikoppeling-test.gemeentehw.nl/opentunnel/00000001825766096000/openzaak/zaakdms/catalogi/api/v1/informatieobjecttypen/3beec26e-e43f-4fd2-ba09-94d47316d877';

        return $args;
    }

    public function connectZaakToInformationObject(array $zaak, array $informationObject): array
    {
        $args = [
            'informatieobject' => $informationObject['url'] ?? '',
            'zaak' => $zaak['url'] ?? '',
            'titel' => $informationObject['titel'] ?? '',
            'status' => 'concept'
        ];

        return $this->request($this->makeURL($this->zaakConnectioninformationObject), 'POST', $args, 'hoi');
    }
}
