<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Foundation\Helpers;

use OWC\Zaaksysteem\Foundation\Plugin;

function app(): Plugin
{
    return resolve('app');
}

function make(string $name, $container)
{
    return Plugin::getInstance()->getContainer()->set($name, $container);
}

function storage_path(string $path = ''): string
{
    return \ABSPATH . '../../storage/' . $path;
}

function resolve($container, $arguments = [])
{
    return Plugin::getInstance()->getContainer()->get($container, $arguments);
}

/**
 * Get a config entry.
 */
function config(string $setting = '', $default = '')
{
    return resolve('config')->get($setting, $default);
}

/**
 * Return a view.
 */
function view(string $template, array $vars = []): string
{
    $view = resolve(\OWC\Zaaksysteem\Foundation\View::class);

    if (! $view->exists($template)) {
        return '';
    }

    return $view->render($template, $vars);
}

/**
 * Get the current selected supplier on a per form basis.
 * Returns label as default, use parameter $getKey to return the key from the config array.
 */
function get_supplier(array $form, bool $getKey = false): string
{
    $allowed = config('suppliers', []);
    $supplier = $form[sprintf('%s-form-setting-supplier', OWC_GZ_PLUGIN_SLUG)] ?? '';

    if (! is_array($allowed) || empty($allowed) || empty($supplier)) {
        return '';
    }

    if (! in_array($supplier, array_keys($allowed))) {
        return '';
    }

    if ($getKey) {
        return $supplier;
    }

    return $allowed[$supplier] ?? '';
}
