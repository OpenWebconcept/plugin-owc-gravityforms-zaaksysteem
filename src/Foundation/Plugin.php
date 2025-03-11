<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Foundation;

use DI\Container;
use DI\ContainerBuilder;
use Exception;

use function OWC\Zaaksysteem\Foundation\Helpers\resolve;

/**
 * BasePlugin which sets all the service providers.
 */
class Plugin
{
    public const NAME = OWC_GZ_PLUGIN_SLUG;
    public const VERSION = OWC_GZ_VERSION;

    protected string $rootPath;
    public Config $config;
    public Loader $loader;
    protected Container $container;

    /**
     * @var Plugin
     */
    protected static $instance;

    /**
     * Constructor of the BasePlugin
     */
    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
        require_once __DIR__ . '/Helpers.php';

        $this->buildContainer();
    }

    /**
     * Return the Plugin instance
     */
    public static function getInstance($rootPath = ''): self
    {
        if (null == static::$instance) {
            static::$instance = new static($rootPath);
        }

        return static::$instance;
    }

    protected function buildContainer(): void
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            'app' => $this,
            self::class => $this,
            'config' => function () {
                return (new Config($this->rootPath . '/config'))->setProtectedNodes(['core']);
            },
            'loader' => Loader::getInstance(),

        ]);
        $builder->addDefinitions($this->rootPath . '/config/container.php');
        $this->container = $builder->build();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function boot(): bool
    {
        $this->config = resolve('config');
        $this->loader = resolve('loader');

        $this->loadTextDomain();
        $this->config->boot();

        $dependencyChecker = new DependencyChecker($this->config->get('core.dependencies'));
        $dependencyChecker->execute();

        if ($dependencyChecker->failed()) {
            $dependencyChecker->notifyFailed();
            deactivate_plugins(plugin_basename($this->rootPath . '/' . $this->getName() . '.php'));

            return false;
        }

        if ($dependencyChecker->optional()) {
            $dependencyChecker->notifyOptional();
        }

        $this->callServiceProviders('boot');

        $this->loader->register();

        return true;
    }

    public function loadTextDomain(): void
    {
        load_plugin_textdomain('owc-gravityforms-zaaksysteem', false, 'owc-gravityforms-zaaksysteem' . '/languages/');
    }

    /**
     * Get the path to a particular resource.
     */
    public function resourceUrl(string $file, string $directory = ''): string
    {
        $directory = ! empty($directory) ? $directory . '/' : '';

        return plugins_url("resources/{$directory}/{$file}", OWC_GZ_PLUGIN_SLUG . '/plugin.php');
    }

    /**
     * Call method on service providers.
     *
     * @throws Exception
     */
    public function callServiceProviders(string $method, string $key = ''): void
    {
        $offset = $key ? "core.providers.{$key}" : 'core.providers';
        $services = $this->config->get($offset);

        foreach ($services as $service) {
            if (is_array($service)) {
                continue;
            }

            $service = $this->container->get($service);

            if (! $service instanceof ServiceProvider) {
                throw new Exception('Provider must be an instance of ServiceProvider.');
            }

            if (method_exists($service, $method)) {
                $service->$method();
            }
        }
    }

    /**
     * Get the name of the plugin.
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * Get the version of the plugin.
     */
    public function getVersion(): string
    {
        return static::VERSION;
    }

    /**
     * Return root path of plugin.
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }
}
