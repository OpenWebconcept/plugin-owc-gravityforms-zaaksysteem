<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\EnableU;

use function OWC\Zaaksysteem\Foundation\Helpers\decrypt;
use OWC\Zaaksysteem\Repositories\EnableU\Classes\InformationObjectHelper;
use OWC\Zaaksysteem\Repositories\EnableU\Classes\PDFHelper;
use OWC\Zaaksysteem\Traits\InformationObject;

class CreateZaakRepository extends BaseRepository
{
    use InformationObject;

    protected string $zakenURI = 'zaken/api/v1/zaken';
    protected string $zakenRolURI = 'zaken/api/v1/rollen';
    protected string $informationObjectsURI = 'documenten/api/v1/enkelvoudiginformatieobjecten';
    protected string $zaakConnectioninformationObject = 'documenten/api/v1/zaakinformatieobjecten';
    protected string $zaakEigenschappenURI = '/zaken/%s/zaakeigenschappen';
    protected PDFHelper $pdfHelper;
    protected InformationObjectHelper $informationObjectHelper;

    public function __construct()
    {
        parent::__construct();

        $this->pdfHelper = new PDFHelper;
        $this->informationObjectHelper = new InformationObjectHelper;
    }

    public function createOpenZaak(array $args = []): array
    {
        if (! empty($args['informatieobject'])) {
            unset($args['informatieobject']);
        }
        
        return $this->request($this->makeURL($this->zakenURI), 'POST', $args);
    }

    public function addZaakProperties(array $zaak, array $args): void
    {
        $zaakEigenschappenURI = sprintf($this->zaakEigenschappenURI, $zaak['uuid']);

        $result = [];
        $numberOfProperties = count($args);

        foreach($args as $key => $arg) {
            $preparedBody = [
                'zaak' => $zaak['url'],
                'eigenschap' => $key,
                'waarde' => $arg
            ];

            $result[] = $this->request($this->makeURL($zaakEigenschappenURI), 'POST', $preparedBody);
        }

        $numberOfRequestsSucces = count(array_filter($result));

        if($numberOfProperties !== $numberOfRequestsSucces) {
            // log?
        }
    }

    /**
     * Add a submitter to a `zaak`.
     * RolTypes needs to be fetched from external source, this part is not implemented yet by the supplier.
     */
    public function createSubmitter(string $zaakUrl, string $bsn): array
    {
        if (empty($zaakUrl) || empty($bsn)) {
            return [];
        }

        $personConcernedURL = $this->createPersonConcernedURL(decrypt($bsn));

        if (empty($personConcernedURL)) {
            return [];
        }

        $data = [
            'zaak' => $zaakUrl,
            'betrokkene' => $personConcernedURL,
            'betrokkeneType' => 'natuurlijk_persoon',
            'roltype' => 'https://digikoppeling-test.gemeentehw.nl/opentunnel/00000001825766096000/openzaak/zaakdms/catalogi/api/v1/roltypen/b8000219-345f-4b3a-9378-d999c689e216',
            'roltoelichting' => 'De indiener van de zaak.',
            'omschrijving' => 'indiener',
            'omschrijvingGeneriek' => 'indiener',
            'registratiedatum' => date('Y-m-d') // Possibly change format to '2023-04-19T09:56:58.277634Z'
        ];

        return $this->request($this->makeURL($this->zakenRolURI), 'POST', $data);
    }

    /**
     * Create person concerned URL based on BSN.
     * Base URL is retrieved by the prefill plugin.
     */
    protected function createPersonConcernedURL(string $bsn): string
    {
        // Below is a default value, needs to be removed when in production.
        if (! class_exists('OWC\PrefillGravityForms\GravityForms\GravityFormsSettings')) {
            return sprintf('https://digikoppeling.overheidsservicebus.com/opentunnel/00000001825766096000/acc/yard-key2dds/brp/ingeschrevenpersonen/%s', $bsn);
        }

        $settings = \OWC\PrefillGravityForms\GravityForms\GravityFormsSettings::make();

        if (empty($settings->getBaseURL())) {
            return '';
        }

        return sprintf('%s/%s', $settings->getBaseURL(), $bsn);
    }

    /**
     * Get the generated PDF and send to the supplier as an information object.
     * Finally connect this object to the 'zaak'.
     */
    public function addFormSubmissionPDF(array $zaak, array $form, array $entry, $args): array
    {
        if(! class_exists('GPDFAPI')) {
            return [];
        }
        
        $pdfFormSettingID = $this->pdfHelper->pdfFormSettingID($entry, $form);

        if (empty($pdfFormSettingID)) {
            return [];
        }
        
        $pdfURL = $this->pdfHelper->pdfURL($entry, $pdfFormSettingID);

        if (empty($pdfURL)) {
            return [];
        }

        // Enable the public access setting so the args can be prepared.
        $this->pdfHelper->updatePublicAccessSettingPDF($form, $pdfFormSettingID, 'enable');

        $args = $this->informationObjectHelper->getPreservedInformationObjectArgs($args);
        $newArgs = $this->pdfHelper->prepareFormSubmissionArgsPDF($this->pdfHelper->createFileName($zaak, $form), $pdfURL);

        $args = array_merge($args, $newArgs);
        unset($args['informatieobject']);
        
        // Disable the public access setting again so the PDF stays protected.
        $this->pdfHelper->updatePublicAccessSettingPDF($form, $pdfFormSettingID);

        $informationObjectResult = $this->request($this->makeURL($this->informationObjectsURI), 'POST', $args);

        return $this->connectZaakToInformationObject($zaak, $informationObjectResult);
    }

    /**
     * Argument 'Informatieobject' is an array which contains urls of the information objects.
     * Add information objects and connect them to the 'zaak' seperatly.
     */
    public function handleZaakInformationObjects(array $args, array $zaak): bool
    {
        $holder = $args;
        $numberOfObjects = count($args['informatieobject']);
        $numberOfMadeConnections = 0;

        foreach ($args['informatieobject'] as $object) {
            if (empty($object['url']) || empty($object['type'])) {
                continue;
            }
            
            $holder['informatieobject'] = $object['url'];
            $createdInformationObject = $this->addInformationObjectToZaak($holder, $object['type']);
            $connectionResult = $this->connectZaakToInformationObject($zaak, $createdInformationObject);

            if ($connectionResult) {
                $numberOfMadeConnections++;
            }
        }

        // Number of objects should match with the number of the connections made.
        return $numberOfObjects === $numberOfMadeConnections;
    }

    protected function addInformationObjectToZaak(array $args = [], $documentType): array
    {
        $args = $this->informationObjectHelper->prepareInformationObjectArgs($args, $documentType);

        return $this->request($this->makeURL($this->informationObjectsURI), 'POST', $args);
    }

    protected function connectZaakToInformationObject(array $zaak, array $informationObject): array
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
