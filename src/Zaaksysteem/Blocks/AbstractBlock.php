<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks;

abstract class AbstractBlock
{
    /**
     * Register block server side.
     */
    public function register($blockName, $attributes): void
    {
        $attributes = $this->hydrateAttributes($attributes, $blockName);

        add_action('init', function () use ($blockName, $attributes) {
            \register_block_type($blockName, $attributes);
        });
    }

    public function hydrateAttributes(array $attributes = []): array
    {
        if (! empty($attributes['render_callback'])) {
            return $attributes;
        }

        $attributes['render_callback'] = [
            sprintf('OWC\Zaaksysteem\Blocks\%s\Block', $this->getClassName()),
            'render'
        ];

        return $attributes;
    }

    /**
     * Return the class of the block.
     */
    public function getClassName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
