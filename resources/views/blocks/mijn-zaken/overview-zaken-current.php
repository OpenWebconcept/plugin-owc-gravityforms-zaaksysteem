<?php

use function OWC\Zaaksysteem\Foundation\Helpers\view;

?>

<div class="zaak-card-wrapper">
	<?php foreach ($vars['zaken'] as $zaak) {
	    if (! empty($zaak->hasEndDate())) {
	        continue;
	    }
	    echo view('blocks/mijn-zaken/tabs-view/zaak-card.php', [
	        'title' => $zaak->title(),
	        'date' => $zaak->startDate('j F Y'),
	        'isActive' => true,
	        'link' => $zaak->permalink(),
	    ]);
	} ?>
</div>
