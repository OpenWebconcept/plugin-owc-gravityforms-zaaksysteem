<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Foundation\Helpers;

use DateTime;
use Exception;

use OWC\Zaaksysteem\Foundation\Plugin;
use OWC\Zaaksysteem\Foundation\Cryptor;

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
 * Encrypt a string.
 */
function encrypt($string): string
{
    try {
        $encrypted = resolve(Cryptor::class)->encrypt($string);
    } catch (\Exception $e) {
        $encrypted = '';
    }

    return (string) $encrypted;
}

/**
 * Decrypt a string.
 */
function decrypt($string): string
{
    try {
        $decrypted = resolve(Cryptor::class)->decrypt($string);
    } catch (\Exception $e) {
        $decrypted = '';
    }

    return (string) $decrypted;
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

/**
 * Add form field values to arguments required for creating a 'Zaak'.
 * Mapping is done by the relation between arguments keys and form fields linkedFieldValueZGWs.
 */
function field_mapping(array $fields, array $entry): array
{
    $mappedFields = [];

    foreach ($fields as $field) {
        if (empty($field->linkedFieldValueZGW)) {
            continue;
        }

        $property = \rgar($entry, (string)$field->id);

        if (empty($property)) {
            continue;
        }

        if ($field->type === 'date') {
            try {
                $property = (new DateTime($property))->format('Y-m-d');
            } catch (Exception $e) {
                $property = '0000-00-00';
            }
        }

        $mappedFields[$field->id] = [
            'eigenschap' => $field->linkedFieldValueZGW,
            'waarde' => $property
        ];
    }

    return $mappedFields;
}
