<?php declare(strict_types=1);
get_header(); ?>
<main class="page-main">
    <div class="container | bg-white p-3 p-md-4 p-lg-5 my-5 shadow">
        <header class="section__header pb-3">
            <h1 class="section__title">
                Er ging iets fout!
            </h1>
            <div class="row">
                <div class="col-md-12">
                    <div id="readspeaker_button1" class="rs_skip rsbtn rs_preserve mb-3">
                        <a rel="nofollow" class="rsbtn_play" accesskey="L"
                            title="Laat de tekst voorlezen met ReadSpeaker webReader"
                            href="//app-eu.readspeaker.com/cgi-bin/rsent?customerid=8150&amp;lang=nl_nl&amp;readid=readspeaker&amp;url=<?php echo get_permalink() ?>">
                            <span class="rsbtn_left rsimg rspart"><span class="rsbtn_text"><span>Lees
                                        voor</span></span></span>
                            <span class="rsbtn_right rsimg rsplay rspart"></span>
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <div class="row">
            <div class="col-lg-12">
                <div id="readspeaker">
                    Het aanmaken van uw zaak is mislukt.
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer();
