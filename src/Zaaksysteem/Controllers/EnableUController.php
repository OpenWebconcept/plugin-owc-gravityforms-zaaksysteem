<?php

namespace OWC\Zaaksysteem\Controllers;

use OWC\Zaaksysteem\Repositories\EnableU\CreateZaakRepository;

class EnableUController extends BaseController
{
    protected CreateZaakRepository $repository;

    public function __construct(array $form, array $entry)
    {
        parent::__construct($form, $entry);

        $this->repository = new CreateZaakRepository;
    }

    public function handle(): bool
    {
        $args = $this->handleArgs();

        if (! empty($args['informatieobject'])) {
            $this->hasInformationObject = true;
        }

        $createdZaak = $this->repository->createOpenZaak($args);

        if (! $createdZaak) {
            return [];
        }

        $this->repository->addZaakProperties($createdZaak, $this->handleArgsZaakProperties());

        $bsn = $this->getBSN();

        if (! empty($bsn) && is_string($bsn)) {
            $this->repository->createSubmitter($createdZaak['url'], $bsn);
        }

        $this->repository->addFormSubmissionPDF($createdZaak, $this->form, $this->entry, $args);

        if ($this->hasInformationObject) {
            $createdConnections = $this->repository->handleZaakInformationObjects($args, $createdZaak);
        }

        return $createdConnections && $createdZaak;
    }

    protected function handleArgsZaakProperties(): array
    {
        $args = [
            'ibanNummer' => '',
            'startdatumActiviteit' => '',
            'datumHuwelijkPartnerschap' => '',
            'digitaalAntwoord' => ''
        ];

        return array_filter($this->mapArgs($args));
    }
}
