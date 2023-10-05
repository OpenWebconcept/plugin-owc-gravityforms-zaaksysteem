<?php

use OWC\Zaaksysteem\Support\Collection;

use function OWC\Zaaksysteem\Foundation\Helpers\view;

?>

<?php foreach ($vars['zaken'] as $zaak) : ?>
    <div class="zaak" data-identifier="<?= $zaak->uuid; ?>">
        <?php
        echo view('blocks/mijn-zaken/zaak-collapse-button.php', [
            'title' => $zaak->title(),
            'id' => $zaak->identificatie,
        ]);
    ?>
        <div class="collapse" id="collapse-<?php echo $zaak->identificatie; ?>">
            <div class="zaak-content">
                <?php
                echo view('blocks/mijn-zaken/zaak-meta.php', compact('zaak'));

    echo view('blocks/mijn-zaken/zaak-process-steps.php', [
        'steps' => is_object($zaak->zaaktype) && $zaak->zaaktype->statustypen instanceof Collection ? $zaak->zaaktype->statustypen->sortByAttribute('volgnummer') : [],
        'status_history' => $zaak->statussen,
        'hasNoStatus' => ($zaak->status->statustoelichting ?? '') === 'Niet beschikbaar',
    ]);

    if ($zaak->zaakinformatieobjecten->count() > 0) {
        echo view('blocks/mijn-zaken/zaak-documents.php', [
            'documents' => $zaak->zaakinformatieobjecten
        ]);
    }
    ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>