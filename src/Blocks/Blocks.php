<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Blocks;

class Blocks
{
    protected Blocks $blocks;

    /**
     * Get all the blocks inside a nested directory inside the 'Zaken' directory.
     */
    public function getFiles(): array
    {
        return glob(__DIR__ . '/*/index.php') ?? [];
    }

    /**
     * Get defined classes.
     */
    public function getClasses(): array
    {
        $classes = [];
        $blocksNamespace = 'OWC\Zaaksysteem\Blocks';

        foreach ($this->getFiles() as $file) {
            $directory = explode('/', pathinfo($file, PATHINFO_DIRNAME));
            $class = $blocksNamespace . '\\' . implode('\\', array_slice($directory, -1, 1, true)) . '\\' . implode('\\', array_slice($directory, -1, 1, true));
            if (class_exists($class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}
