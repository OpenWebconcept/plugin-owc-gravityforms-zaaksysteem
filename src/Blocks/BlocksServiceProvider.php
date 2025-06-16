<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks;

use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Foundation\ServiceProvider;

class BlocksServiceProvider extends ServiceProvider
{
    protected Blocks $blocks;

    public function __construct(Plugin $plugin)
    {
        parent::__construct($plugin);
        $this->blocks = new Blocks();
    }

    public function boot(): void
    {
        foreach ($this->blocks->getClasses() as $class) {
            (new $class());
        }

        $this->loadHooks();
    }

    protected function loadHooks(): void
    {
        \add_action('enqueue_block_editor_assets', [$this, 'blockAssets'], 10, 0);
        \add_action('wp_enqueue_scripts', [$this, 'blockAssetsFrontend'], 10, 0);
    }

    public function blockAssets(): void
    {
        \wp_enqueue_script(
            'zaak-editor',
            Plugin::getInstance()->resourceUrl('editor.js', 'dist/build'),
            ['wp-blocks', 'wp-element', 'wp-edit-post', 'wp-dom-ready'],
            time(),
        );

        \wp_enqueue_style(
            'zaak-styles',
            Plugin::getInstance()->resourceUrl('zaak-styles.css', 'dist/build'),
            [],
            time(),
        );
    }

    public function blockAssetsFrontend(): void
    {
        \wp_enqueue_style(
            'zaak-styles',
            Plugin::getInstance()->resourceUrl('zaak-styles.css', 'dist/build'),
            [],
            time(),
        );

        \wp_enqueue_script(
            'zaak-frontend',
            Plugin::getInstance()->resourceUrl('zaak-frontend.js', 'dist/build'),
            [],
            time(),
			true
        );
    }
}
