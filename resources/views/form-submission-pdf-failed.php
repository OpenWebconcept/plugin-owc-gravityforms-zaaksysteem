<?php declare(strict_types=1);
get_header(); ?>
<main id="main" class="template-openzaak">
    <div class="container">
        <div class="container-inner">
            <header>
                <h1>
                    Er ging iets fout!
                </h1>
            </header>
            <div class="content">
                <div id="readspeaker">
                    <p class="mb-1">
						Uw zaak is succesvol aangemaakt, echter is het document met de originele aanvraag niet gegenereerd. Excuses voor het ongemak. De zaak is wel in goede orde ontvangen.
                    </p>
					<?php if (! empty($vars['error'])) : ?>
                        <p>Gebruik het volgende bericht in uw correspondentie met ons: "<?= $vars['error']; ?>"</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer();
