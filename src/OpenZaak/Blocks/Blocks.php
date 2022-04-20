<?php declare(strict_types=1);

namespace OWC\OpenZaak\Blocks;

class Blocks
{
    /**
     * Get all the blocks.
     *
     * @return array
     */
    public function getFiles(): array
    {
        return glob(__DIR__ . '/*/index.php') ?? [];
    }

    /**
     * Get defined classes.
     *
     * @return array
     */
    public function getClasses(): array
    {
        $classes = [];
        $blocksNamespace = 'OWC\OpenZaak\Blocks';

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
