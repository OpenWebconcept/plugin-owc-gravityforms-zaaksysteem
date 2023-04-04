<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\EnableU;

use OWC\Zaaksysteem\Traits\InformationObject;

class CreateZaakRepository extends BaseRepository
{
    use InformationObject;

    protected string $zakenURI = 'zaken/api/v1/zaken';
    protected string $informationObjectsURI = 'documenten/api/v1/enkelvoudiginformatieobjecten';

    public function __construct()
    {
        parent::__construct();
    }

    public function createOpenZaak(array $args = []): array
    {
        if (! empty($args['informatieobject'])) {
            unset($args['informatieobject']);
        } else {
            // Loggen?
            return [];
        }
        
        return $this->request($this->makeURL($this->zakenURI), 'POST', $args);
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

    protected function handleInformationObjectArgs(array $args)
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
}
