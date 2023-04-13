<?php

declare(strict_types=1);

use function OWC\Zaaksysteem\Foundation\Helpers\view;

?>

<?php foreach ($vars['zaken'] as $zaak) : ?>
    <div class="zaak" data-identifier="<?= $zaak->uuid; ?>">
        <?php
            echo view('mijn-zaken/zaak-collapse-button.php', [
                'title' => $zaak->title(),
                'id' => $zaak->identificatie,
            ]);
        ?>
        <div class="collapse"
            id="collapse-<?php echo $zaak->identificatie; ?>">
            <div class="zaak-content">
                <?php
                    echo view('mijn-zaken/zaak-meta.php', compact('zaak'));

                    echo view('mijn-zaken/zaak-process-steps.php', [
                        'steps' => $zaak->zaaktype->statustypen->sortByAttribute('volgnummer'),
                        'status_history' => $zaak->statussen,
                        'currentStep' => $zaak->status->statustoelichting,
                        'hasNoStatus' => $zaak->status->statustoelichting === 'Niet beschikbaar',
                    ]);

                if ($zaak->zaakinformatieobjecten->count() > 0) {
                    echo view('mijn-zaken/zaak-documents.php', [
                        'documents' => $zaak->zaakinformatieobjecten
                    ]);
                }
                ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>