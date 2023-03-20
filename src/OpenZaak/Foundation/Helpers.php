<?php

declare(strict_types=1);

namespace OWC\OpenZaak\Foundation\Helpers;

use OWC\OpenZaak\Foundation\Plugin;

function app(): Plugin
{
    return resolve('app');
}

function make($name, $container)
{
    return \OWC\OpenZaak\Foundation\Plugin::getInstance()->getContainer()->set($name, $container);
}

function storage_path(string $path = ''): string
{
    return \ABSPATH . '../../storage/' . $path;
}

function resolve($container, $arguments = [])
{
    return \OWC\OpenZaak\Foundation\Plugin::getInstance()->getContainer()->get($container, $arguments);
}

/**
 * Encrypt a string.
 *
 * @param string $string
 * @return string
 */
function encrypt($string): string
{
    return resolve(\OWC\OpenZaak\Foundation\Cryptor::class)->encrypt($string);
}
/**
 * Decrypt a string.
 *
 * @param string $string
 * @return string
 */
function decrypt($string): string
{
    return resolve(\OWC\OpenZaak\Foundation\Cryptor::class)->decrypt($string);
}

/**
 * Get a config entry.
 *
 * @param string $setting
 * @param string $default
 *
 * @return string
 */
function config(string $setting, string $default = ''): ?string
{
    return resolve('config')->get($setting, $default);
}

/**
 * Return a view.
 *
 * @param string $template
 * @param array $vars
 * @return string
 */
function view(string $template, array $vars = []): string
{
    return resolve(\OWC\OpenZaak\Foundation\View::class)->render($template, $vars);
}
