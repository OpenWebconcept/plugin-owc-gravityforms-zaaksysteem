<?php declare(strict_types=1);

namespace OWC\OpenZaak\Blocks;

abstract class AbstractBlock
{
    /*
     * Register block serverside.
     */
    public function register($blockName, $attributes): void
    {
        $attributes = $this->hydrateAttributes($attributes, $blockName);

        add_action('init', function () use ($blockName, $attributes) {
            // var_dump($blockName, $attributes);
            // die;
            \register_block_type($blockName, $attributes);
        });
    }

    public function hydrateAttributes(array $attributes = []): array
    {
        if (isset($attributes['render_callback'])) {
            return $attributes;
        }

        $attributes['render_callback'] = [
            sprintf('Yard\Blocks\Blocks\%s\Entities\Block', $this->getClassName()),
            'render'
        ];

        return $attributes;
    }
}
