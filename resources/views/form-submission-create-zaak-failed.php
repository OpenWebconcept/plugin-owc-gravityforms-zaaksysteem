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
                        Het aanmaken van uw zaak is mislukt.
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
