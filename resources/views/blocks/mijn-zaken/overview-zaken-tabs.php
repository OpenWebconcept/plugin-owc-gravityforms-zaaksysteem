<?php

use function OWC\Zaaksysteem\Foundation\Helpers\view;

?>

<ul class="zaak-tabs | nav nav-tabs" id="zaak-tabs" role="tablist">
	<li class="nav-item" role="presentation">
		<button class="zaak-tabs-link | nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab" aria-controls="current" aria-selected="true">Lopende zaken</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="zaak-tabs-link | nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">Afgeronde zaken</button>
	</li>
</ul>

<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="current" role="tabpanel" aria-labelledby="current-tab">
		<div class="zaak-card-wrapper">
			<?php foreach ($vars['zaken'] as $zaak) {
			    echo view('blocks/mijn-zaken/tabs-view/zaak-card.php', [
			        'title' => $zaak->title(),
			        'date' => $zaak->startDate('j F Y'),
			        // 'tag' => '1 taak open', // Dummy data
			        'isActive' => true,
			        'link' => $zaak->permalink()
			    ]);
			} ?>
		</div>
	</div>

	<div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
		<div class="zaak-card-wrapper">
			<?php foreach ($vars['zaken'] as $zaak) {
			    echo view('blocks/mijn-zaken/tabs-view/zaak-card.php', [
			        'title' => $zaak->title(),
			        'date' => $zaak->startDate('j F Y'),
			    ]);
			} ?>
		</div>
	</div>
</div>
