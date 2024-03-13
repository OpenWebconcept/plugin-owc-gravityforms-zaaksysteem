<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Foundation;

/**
 * Checks if dependencies are valid.
 */
class DependencyChecker
{
    /**
     * Plugins that need to be checked for.
     */
    private array $dependencies;

    /**
     * Build up array of failed plugins, either because
     * they have the wrong version or are inactive.
     */
    private array $failed = [];

    /**
     * Build up array of optional plugins.
     */
    private array $optional = [];

    /**
     * Determine which plugins need to be present.
     */
    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    public function execute(): void
    {
        foreach ($this->dependencies as $dependency) {
            switch ($dependency['type']) {
                case 'class':
                    $this->checkClass($dependency);

                    break;
                case 'plugin':
                    $this->checkPlugin($dependency);

                    break;
            }
        }
    }

    /**
     * Determines if the dependencies are not met.
     */
    public function failed(): bool
    {
        return 0 < count($this->failed);
    }

    /**
     * Determines if there are optional dependencies are not met.
     */
    public function optional(): bool
    {
        return 0 < count($this->optional);
    }

    /**
     * Notifies the administrator which plugins need to be enabled,
     * or which plugins have the wrong version.
     */
    public function notifyFailed(): void
    {
        add_action('admin_notices', function () {
            $list = '<p>' . sprintf(esc_html__('De volgende plug-ins zijn vereist voor het gebruik van de %1$s plugin:', 'owc-gravityforms-zaaksysteem'), OWC_GZ_NAME) . '</p><ol>';

            foreach ($this->failed as $dependency) {
                $info = isset($dependency['message']) ? ' (' . $dependency['message'] . ')' : '';
                $list .= sprintf('<li>%s%s</li>', $dependency['label'], $info);
            }

            $list .= '</ol>';

            printf('<div class="notice notice-error"><p>%s</p></div>', $list);
        });
    }

    public function notifyOptional(): void
    {
        add_action('admin_notices', function () {
            $list = '<p>' . sprintf(esc_html__('De volgende plug-ins zijn niet vereist voor het gebruik van de %1$s plugin maar worden wel aangeraden om te gebruiken:', 'owc-gravityforms-zaaksysteem'), OWC_GZ_NAME) . '</p><ol>';

            foreach ($this->optional as $dependency) {
                $info = isset($dependency['message']) ? ' (' . $dependency['message'] . ')' : '';
                $list .= sprintf('<li>%s%s</li>', $dependency['label'], $info);
            }

            $list .= '</ol>';

            printf('<div class="notice notice-info"><p>%s</p></div>', $list);
        });
    }

    /**
     * Marks a dependency as failed.
     */
    private function markFailed(array $dependency, string $defaultMessage): void
    {
        $this->failed[] = array_merge([
            'message' => $dependency['message'] ?? $defaultMessage,
        ], $dependency);
    }

    /**
     * Marks a dependency as optional.
     */
    private function markOptional(array $dependency, string $defaultMessage): void
    {
        $this->optional[] = array_merge([
            'message' => $dependency['message'] ?? $defaultMessage,
        ], $dependency);
    }

    /**
     * Checks if required class exists.
     */
    private function checkClass(array $dependency): void
    {
        if (! class_exists($dependency['name'])) {
            $this->markFailed($dependency, esc_html__('Klasse bestaat niet', 'owc-gravityforms-zaaksysteem'));

            return;
        }
    }

    /**
     * Check if a plugin is enabled and has the correct version.
     */
    private function checkPlugin(array $dependency): void
    {
        if (! function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (! empty($dependency['optional']) && ! is_plugin_active($dependency['file'])) {
            $this->markOptional($dependency, esc_html__('Optioneel', 'owc-gravityforms-zaaksysteem'));

            return;
        }

        if (! is_plugin_active($dependency['file'])) {
            $this->markFailed($dependency, esc_html__('Inactief', 'owc-gravityforms-zaaksysteem'));

            return;
        }

        // If there is a version lock set on the dependency...
        if (isset($dependency['version'])) {
            if (! $this->checkVersion($dependency)) {
                $this->markFailed($dependency, esc_html__('Minimale versie:', 'owc-gravityforms-zaaksysteem') . ' <b>' . $dependency['version'] . '</b>');
            }
        }
    }

    /**
     * Checks the installed version of the plugin.
     */
    private function checkVersion(array $dependency): bool
    {
        $file = file_get_contents(WP_PLUGIN_DIR . '/' . $dependency['file']);

        preg_match('/^(?: ?\* ?Version: ?)(.*)$/m', $file, $matches);
        $version = isset($matches[1]) ? str_replace(' ', '', $matches[1]) : '0.0.0';

        return version_compare($version, $dependency['version'], '>=');
    }
}
