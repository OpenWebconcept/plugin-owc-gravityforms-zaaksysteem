<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms;

use OWC\Zaaksysteem\Entities\Zaak;
use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Http\Errors\ResourceNotFoundError;

use function OWC\Zaaksysteem\Foundation\Helpers\get_supplier;
use function OWC\Zaaksysteem\Foundation\Helpers\view;

class GravityForms
{
    /**
     * Instance of the plugin.
     */
    protected Plugin $plugin;

    protected string $supplier;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Get and set the selected Zaaksysteem supplier.
     */
    protected function setSupplier(array $form): void
    {
        $this->supplier = get_supplier($form);
    }

    /**
     * Handle what happens after submitting the form.
     */
    public function GFFormSubmission(array $entry, array $form)
    {
        $this->setSupplier($form);

        // if there is no supplier set just return the form.
        if (empty($this->supplier)) {
            return $form;
        }

        $result = $this->createZaak($entry, $form);

        if (empty($result)) {
            echo view('form-submission-failed.php');
            exit;
        }

        return $form;
    }

    /**
     * Create a new Zaak.
     */
    protected function createZaak(array $entry, array $form): ?Zaak
    {
        $action = sprintf('OWC\Zaaksysteem\Clients\%s\Actions\CreateZaakAction', $this->supplier);

        if (! class_exists($action)) {
            // REFERENCE POINT: Mike -> catch and log or show critical error has occurred?
            throw new ResourceNotFoundError(sprintf('Class "%s" does not exists. Verify if the selected supplier has an action class', $action));
        }

        $instance = new $action($this->plugin);

        return $instance->addZaak($entry, $form);
    }
}
