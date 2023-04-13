<?php

declare(strict_types=1);

use function OWC\Zaaksysteem\Foundation\Helpers\view;

?>

<div class="zaak">
    <?php
    echo view('partials/zaak-collapse-button.php', [
        'title' => sprintf('%s, %s', $zaak->getDesc(), $zaak->getRegistrationDate()),
        'id' => $zaak->getIdentification(),
    ]);
?>
    <div class="collapse"
        id="collapse-<?php echo $zaak->getIdentification(); ?>">
        <div class="zaak-content">
            <?php
                echo view('partials/zaak-meta.php', [
                    'zaak' => $zaak,
                ]);

echo view('partials/zaak-process-steps.php', [
    'steps' => $zaak->getStatusTypes(),
    'currentStep' => $zaak->getStatusDesc(),
    'hasNoStatus' => $zaak->getStatusDesc() === 'Niet beschikbaar',
]);

echo view('partials/zaak-documents.php', [
    'documents' => [],
]);
?>
        </div>
    </div>
</div>