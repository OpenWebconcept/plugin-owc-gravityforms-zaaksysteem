<?php

declare(strict_types=1);

?>
<li class="zaak-process-steps__step <?php echo $vars['step']['isChecked'] ? 'zaak-process-steps__step--is-checked' : ''; ?>"
    aria-current="">
    <button class="zaak-process-steps__step-heading">
        <div class="zaak-process-steps__step-marker">
        </div>
        <div class="zaak-process-steps__step-heading-label">
            <?php echo $vars['step']['title']; ?>
        </div>
    </button>
    <?php if ($vars['step']['substeps']) : ?>
    <ol class="zaak-process-steps__sub-step-list">
        <?php foreach ($step['substeps'] ?? [] as $substep) : ?>
        <li class="zaak-process-steps__ sub-step">
            <div class="zaak-process-steps__sub-step-marker"></div>
            <p class="zaak-process-steps__sub-step-heading"><?php echo $substep['title']; ?>
            </p>
        </li>
        <?php endforeach; ?>
    </ol>
    <?php endif; ?>
</li>