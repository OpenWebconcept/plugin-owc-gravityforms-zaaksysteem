<div class="zaak-documents">
    <h3>Documenten</h3>
    <?php if (empty($vars['documents'])) : ?>
        <p>Er zijn geen documenten gevonden.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($vars['documents'] as $document) : ?>
                <li>
                    <?php

                    /**
                     * @todo build proxy to download files
                     */

                    ?>
                    <a href="#">
                        <?= $document->titel; ?> (TODO)
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>