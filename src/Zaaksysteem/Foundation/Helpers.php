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
    return \OWC\Zaaksysteem\Foundation\Plugin::getInstance()->getContainer()->set($name, $container);
}

function storage_path(string $path = ''): string
{
    return \ABSPATH . '../../storage/' . $path;
}

function resolve($container, $arguments = [])
{
    return \OWC\Zaaksysteem\Foundation\Plugin::getInstance()->getContainer()->get($container, $arguments);
}

/**
 * Encrypt a string.
 */
function encrypt($string): string
{
    try {
        $encrypted = resolve(\OWC\Zaaksysteem\Foundation\Cryptor::class)->encrypt($string);
    } catch(\Exception $e) {
        $encrypted = '';
    }

    return $encrypted;
}

/**
 * Decrypt a string.
 */
function decrypt($string): string
{
    try {
        $decrypted = resolve(\OWC\Zaaksysteem\Foundation\Cryptor::class)->decrypt($string);
    } catch(\Exception $e) {
        $decrypted = '';
    }

    return $decrypted;
}

/**
 * Get a config entry.
 */
function config(string $setting, string $default = ''): ?string
{
    return resolve('config')->get($setting, $default);
}

/**
 * Return a view.
 */
function view(string $template, array $vars = []): string
{
    return resolve(\OWC\Zaaksysteem\Foundation\View::class)->render($template, $vars);
}

/**
 * Get the current selected supplier on a per form basis.
 */
function get_supplier(array $form): string
{
    $allowed = ['openzaak', 'decos-join', 'none'];
    $supplier = $form[OZ_PLUGIN_SLUG . '-form-setting-supplier'];

    if (!in_array($supplier, $allowed)) {
        return 'none';
    }

    return $supplier;
}
