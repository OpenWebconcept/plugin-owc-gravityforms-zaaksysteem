<?php declare(strict_types=1);

namespace OWC\OpenZaak\Blocks;

use OWC\OpenZaak\Foundation\Plugin;
use OWC\OpenZaak\Foundation\ServiceProvider;

class BlocksServiceProvider extends ServiceProvider
{
    public function __construct($plugin)
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
    }

    public function blockAssets(): void
    {
        \wp_enqueue_script(
            'theme-blocks',
            Plugin::getInstance()->resourceUrl('editor.js', 'dist/build'),
            ['wp-blocks', 'wp-element', 'wp-edit-post', 'wp-dom-ready'],
            time(),
        );
    }
}
