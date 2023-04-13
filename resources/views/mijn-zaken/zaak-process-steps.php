<?php

declare(strict_types=1);

use function OWC\Zaaksysteem\Foundation\Helpers\view;

?>

<div class="zaak-process">
    <h3>Status</h3>
    <?php if (empty($vars['steps']) || $vars['hasNoStatus']) : ?>
        <p>Momenteel is er geen status beschikbaar.</p>
    <?php else : ?>
    <ol class="zaak-process-steps">
        <?php foreach ($vars['steps'] as $step) : ?>
            <?php
                echo view('partials/zaak-process-step.php', [
                    'step' => $step,
                    'isCurrent' => strtolower($step->omschrijving ?? '') === strtolower($vars['currentStep'] ?? ''),
                    'isPast' => false,
                ]);
            ?>
        <?php endforeach; ?>
    <?php endif; ?>
    </ol>
</div>