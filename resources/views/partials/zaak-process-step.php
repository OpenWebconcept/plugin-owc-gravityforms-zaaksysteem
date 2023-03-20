<?php

declare(strict_types=1);

use function OWC\OpenZaak\Foundation\Helpers\view;

?>

<li class="zaak-process-steps__step <?php echo $vars['isCurrent'] ? 'zaak-process-steps__step--current' : '';
echo $vars['isPast'] ? 'zaak-process-steps__step--past' : ''; ?>" aria-current="">
    <span class="zaak-process-steps__step-marker">
        <?php echo $vars['isPast'] ? '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M435.848 83.466L172.804 346.51l-96.652-96.652c-4.686-4.686-12.284-4.686-16.971 0l-28.284 28.284c-4.686 4.686-4.686 12.284 0 16.971l133.421 133.421c4.686 4.686 12.284 4.686 16.971 0l299.813-299.813c4.686-4.686 4.686-12.284 0-16.971l-28.284-28.284c-4.686-4.686-12.284-4.686-16.97 0z"/></svg>
        ' : $vars['step']->getNumber() ; ?>
    </span>
    <span class="zaak-process-steps__step-heading-label">
        <?php echo $vars['step']->getDesc() ?>
    </span>
    <?php if (false) : ?>
        <ol class="zaak-process-steps__substeps">
            <?php echo view('partials/zaak-process-substep.php', [
                'substep' => $substep,
            ]); ?>
        </ol>
    <?php endif; ?>
</li>