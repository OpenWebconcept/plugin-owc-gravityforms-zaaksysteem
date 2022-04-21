<?php

declare(strict_types=1);

use function OWC\OpenZaak\Foundation\Helpers\view;

?>

<div class="zaak-process">
    <ol class="zaak-process-steps">
        <h3>Status</h3>
        <?php if (empty($vars['steps'])) : ?>
            <p>Momenteel is er geen status beschikbaar.</p>
        <?php else : ?>
            <?php foreach ($vars['steps'] as $step) : ?>
                <?php echo view('partials/zaak-process-step.php', [
                    'step' => $step,
                ]); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </ol>
</div>