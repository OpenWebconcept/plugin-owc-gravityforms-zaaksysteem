<div class="zaak-documents">
    <h3>Documenten</h3>
    <?php if (empty($vars['documents'])) : ?>
        <p>Er zijn geen documenten gevonden.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($vars['documents'] as $document) : ?>
				<?php if (! empty($document->informatieobject->downloadUrl($vars['zaak']->identification()))) : ?>
					<li>
						<a href="<?= $document->informatieobject->downloadUrl($vars['zaak']->identification()); ?>">
							<?= $document->informatieobject->fileName(); ?> <?php if ($document->informatieobject->formattedMetaData()): ?>(<?= $document->informatieobject->formattedMetaData(); ?>) <?php endif ?>
						</a>
					</li>
				<?php endif ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
