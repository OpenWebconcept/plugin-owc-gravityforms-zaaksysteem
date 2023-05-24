<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Repositories\EnableU\Classes;

use OWC\Zaaksysteem\Traits\InformationObject;

class InformationObjectHelper
{
    use InformationObject;

    protected string $documentType;

    public function __construct(string $documentType)
    {
        $this->documentType = $documentType;
    }

    public function prepareInformationObjectArgs(array $args)
    {
        $args = $this->getPreservedInformationObjectArgs($args);
        $args = $this->handleInformationObjectArgs($args);

        return $args;
    }

    /**
     * Preserve some of the args which were used to create a 'Zaak'.
     */
    public function getPreservedInformationObjectArgs(array $args): array
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

    public function handleInformationObjectArgs(array $args): array
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
        $args['informatieobjecttype'] = sprintf('https://digikoppeling-test.gemeentehw.nl/opentunnel/00000001825766096000/openzaak/zaakdms/catalogi/api/v1/informatieobjecttypen/%s', $this->documentType);

        return $args;
    }
}
