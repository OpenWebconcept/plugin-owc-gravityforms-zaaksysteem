<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Contracts;

use GF_Field;
use OWC\Zaaksysteem\Entities\Enkelvoudiginformatieobject;
use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Entities\Zaakinformatieobject;
use OWC\Zaaksysteem\Resolvers\ContainerResolver;
use OWC\Zaaksysteem\Traits\CheckURL;
use OWC\Zaaksysteem\Traits\InformationObject;
use OWC\Zaaksysteem\Traits\MergeTags;

abstract class AbstractCreateUploadedDocumentsAction
{
    use CheckURL;
    use InformationObject;
    use MergeTags;

    public const CLIENT_NAME = '';

    protected array $entry;
    protected array $form;
    protected Zaak $zaak;
    protected Client $client;

    public function __construct(array $entry, array $form, Zaak $zaak)
    {
        $this->entry = $entry;
        $this->form = $form;
        $this->zaak = $zaak;
        $this->client = ContainerResolver::make()->getApiClient(static::CLIENT_NAME);
    }

    abstract public function addUploadedDocuments(): ?bool;

    /**
     * Extract values from fields which are connected to the 'informatieobject' mapping field.
     */
    protected function mapArgs(array $form, array $entry): array
    {
        $args = ['informatieobject' => ''];

        foreach ($form['fields'] as $field) {
            if (empty($field->linkedFieldValueZGW) || ! isset($args[$field->linkedFieldValueZGW]) || empty($field->linkedFieldValueDocumentType)) {
                continue;
            }

            if ('informatieobject' !== $field->linkedFieldValueZGW) {
                continue;
            }

            $fieldValue = rgar($entry, (string) $field->id);

            if (empty($fieldValue)) {
                continue;
            }

            $args = $this->mapInformationObjectArg($args, $field, $fieldValue);
        }

        return $args;
    }

    /**
     * Fields mapped to 'informatieobject' can contain a simple url but also an array of urls in JSON format.
     */
    protected function mapInformationObjectArg(array $args, GF_Field $field, $fieldValue): array
    {
        $start = substr($fieldValue, 0, 1);
        $end = substr($fieldValue, -1, 1);

        // Check if field value is an array in JSON format and decode.
        if ('[' === $start && ']' === $end) {
            $fieldValue = $this->convertInformationObjectFieldJSON($fieldValue, $field);
        }

        if (is_string($fieldValue)) {
            $fieldValue = [
                ['type' => $field->linkedFieldValueDocumentType, 'url' => $fieldValue, 'description' => $field->linkedUploadFieldDescriptionValueZGW ?? ''],
            ];
        }

        // After previous conversions it's possible the value is empty.
        if (empty($fieldValue)) {
            return $args;
        }

        if (! empty($args[$field->linkedFieldValueZGW])) {
            $args[$field->linkedFieldValueZGW] = array_merge($args[$field->linkedFieldValueZGW], $fieldValue);
        } else {
            $args[$field->linkedFieldValueZGW] = $fieldValue;
        }

        return $args;
    }

    /**
     * Decode to array and return array with documenttype and url of information object.
     */
    protected function convertInformationObjectFieldJSON(string $fieldValue, $field): array
    {
        $fieldValues = json_decode($fieldValue);

        if (empty($fieldValues) || ! is_array($fieldValues)) {
            return [];
        }

        return array_map(function ($fieldValue) use ($field) {
            return ['type' => $field->linkedFieldValueDocumentType, 'url' => $fieldValue, 'description' => $field->linkedUploadFieldDescriptionValueZGW ?? ''];
        }, $fieldValues);
    }

    protected function createInformationObject(array $args): ?Enkelvoudiginformatieobject
    {
        if (empty($args)) {
            return null;
        }

        $object = $this->client->enkelvoudiginformatieobjecten()->create(new Enkelvoudiginformatieobject($args, $this->client->getClientName(), $this->client->getClientNamePretty()));
        $object->setValue('zaak', $this->zaak->url); // Is needed for connecting an informatie object to a zaak.

        return $object;
    }

    protected function connectZaakToInformationObject(?Enkelvoudiginformatieobject $object): ?Zaakinformatieobject
    {
        if (empty($object)) {
            return null;
        }

        return $this->client->zaakinformatieobjecten()->create(new Zaakinformatieobject($object->toArray(), $this->client->getClientName(), $this->client->getClientNamePretty())); // What to do when this one fails?
    }

    protected function prepareInformationObjectArgs(string $objectURL, string $informationObjectType, string $objectDescription): array
    {
        if (empty($informationObjectType)) {
            return [];
        }

        $fileName = $this->createFileName($objectURL);
        $bestandsomvang = $this->getContentLength($objectURL);
        $inhoud = $this->informationObjectToBase64($objectURL);

        $args = [];
        $args['titel'] = $fileName;
        $args['formaat'] = $this->getContentType($objectURL);
        $args['bestandsnaam'] = sprintf('%s.%s', \sanitize_title($fileName), $this->getExtension($objectURL));
        $args['bestandsomvang'] = $bestandsomvang ? (int) $bestandsomvang : strlen($inhoud);
        $args['beschrijving'] = 0 < strlen($objectDescription) ? $this->handleMergeTags($this->entry, $objectDescription) : $fileName;
        $args['inhoud'] = $inhoud;
        $args['vertrouwelijkheidaanduiding'] = 'vertrouwelijk';
        $args['auteur'] = 'OWC';
        $args['status'] = 'gearchiveerd';
        $args['taal'] = 'nld';
        $args['bronorganisatie'] = ContainerResolver::make()->rsin();
        $args['creatiedatum'] = date('Y-m-d');
        $args['informatieobjecttype'] = $informationObjectType;

        return $args;
    }

    protected function createFileName(string $objectURL): string
    {
        $pathInfo = pathinfo($objectURL);
        $fileName = $pathInfo['filename'];

        return sprintf('%s_%s', uniqid(), $fileName);
    }
}
