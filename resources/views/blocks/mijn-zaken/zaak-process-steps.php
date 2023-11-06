<?php

declare(strict_types=1);

use function OWC\Zaaksysteem\Foundation\Helpers\view;

?>

<div class="zaak-process">
    <h2>Status</h2>
    <?php if (empty($vars['steps']) || $vars['hasNoStatus']) : ?>
        <p>Momenteel is er geen status beschikbaar.</p>
    <?php else : ?>
        <ol class="zaak-process-steps">
            <?php foreach ($vars['steps'] as $step) : ?>
                <?php
                if (!empty($vars['status_history'])) {
                    $statusUpdate = $vars['status_history']->filter(function ($status) use ($step) {
                        return $status->statustype->url === $step->url;
                    })->first();
                }
                ?>
                <?php
                echo view('blocks/mijn-zaken/zaak-process-step.php', [
                    'step' => $step,
                    // 'isCurrent' => $step->isEndStatus(),
                    'isCurrent' => $step->statusExplanation() === $vars['currentStep'],
                    'isPast' => !$step->isEndStatus(),
                    'stepUpdate' => $statusUpdate ?? false,
                ]);
                ?>
            <?php endforeach; ?>
        <?php endif; ?>
        </ol>
</div>
