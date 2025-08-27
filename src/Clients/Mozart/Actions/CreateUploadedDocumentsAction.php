<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\Mozart\Actions;

use OWC\Zaaksysteem\Contracts\AbstractCreateUploadedDocumentsAction;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;

class CreateUploadedDocumentsAction extends AbstractCreateUploadedDocumentsAction
{
    public const CLIENT_NAME = 'mozart';

    public function addUploadedDocuments(): ?bool
    {
        $mappedArgs = $this->mapArgs($this->form, $this->entry);

        if (empty($mappedArgs['informatieobject'])) {
            return null;
        }

        $count = count($mappedArgs['informatieobject']);
        $succes = 0;

        foreach ($mappedArgs['informatieobject'] as $object) {
            if (empty($object['url']) || empty($object['type'])) {
                continue;
            }

            if (! $this->checkURL($object['url'])) {
                continue;
            }

            $args = $this->prepareInformationObjectArgs($object['url'], $object['type'], $object['description']);
            $connectionResult = $this->connectZaakToInformationObject($this->createInformationObject($args));

            if ($connectionResult instanceof Zaakinformatieobject) {
                $succes++;
            }
        }

        return $count === $succes;
    }
}
