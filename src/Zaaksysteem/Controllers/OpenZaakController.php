<?php

namespace OWC\Zaaksysteem\Controllers;

use OWC\Zaaksysteem\Repositories\OpenZaak\CreateZaakRepository;

class OpenZaakController extends BaseController
{
    protected CreateZaakRepository $repository;

    public function __construct(array $form, array $entry)
    {
        parent::__construct($form, $entry);

        $this->repository = new CreateZaakRepository;
    }

    public function handle(): array
    {
        $args = $this->handleArgs();

        if (! empty($args['informatieobject'])) {
            $this->hasInformationObject = true;
        }

        $createdZaak = $this->repository->createOpenZaak($args);

        if (! $createdZaak) {
            return [];
        }

        $bsn = $this->getBSN();

        if (! empty($bsn) && is_string($bsn)) {
            $this->repository->createSubmitter($createdZaak['url'], $bsn);
        }

        return $createdZaak;
    }
}
