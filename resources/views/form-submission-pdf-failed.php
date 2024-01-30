<?php declare(strict_types=1);
get_header(); ?>
<main id="main" class="template-openzaak">
    <div class="container">
        <div class="container-inner">
            <header>
                <h1>
                    Er ging iets fout!
                </h1>
                <div id="readspeaker_button1" class="rs_skip rsbtn rs_preserve">
                    <a rel="nofollow" class="rsbtn_play" accesskey="L"
                        title="Laat de tekst voorlezen met ReadSpeaker webReader"
                        href="//app-eu.readspeaker.com/cgi-bin/rsent?customerid=8150&amp;lang=nl_nl&amp;readid=readspeaker&amp;url=<?php echo get_permalink() ?>">
                        <span class="rsbtn_left rsimg rspart"><span class="rsbtn_text"><span>Lees
                                    voor</span></span></span>
                        <span class="rsbtn_right rsimg rsplay rspart"></span>
                    </a>
                </div>
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
