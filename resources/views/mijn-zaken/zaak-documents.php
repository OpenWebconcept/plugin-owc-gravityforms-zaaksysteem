<div class="zaak-documents">
    <h3>Documenten</h3>
    <?php if (empty($vars['documents'])): ?>
        <p>Er zijn geen documenten gevonden.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($vars['documents'] as $document): ?>
                <li>
                    <a href="<?php echo $document->getUrl(); ?>">
                        <?php echo $document->getTitle(); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>