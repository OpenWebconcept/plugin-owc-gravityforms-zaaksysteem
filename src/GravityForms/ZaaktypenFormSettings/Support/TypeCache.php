<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Support;

class TypeCache
{
    private const CACHE_TTL_DEFAULT = 64800; // 18 hours.

    public function get(string $key): ?array
    {
        $data = get_transient($key);

        return is_array($data) ? $data : null;
    }

    public function put(string $key, array $data, int $ttl = 0): void
    {
        $ttl = (int) apply_filters('owc_gravityforms_zaaksysteem_zaaktypen_form_settings_type_cache_ttl', $ttl);

        set_transient($key, $data, $ttl ?: self::CACHE_TTL_DEFAULT);
    }
}
