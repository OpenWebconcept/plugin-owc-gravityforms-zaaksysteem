<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\GravityForms\ZaaktypenFormSettings\Support;

class TypeCache
{
    public function get(string $key): ?array
    {
        $data = get_transient($key);

        return is_array($data) ? $data : null;
    }

    public function put(string $key, array $data, int $ttl = 64800): void
    {
        set_transient($key, $data, $ttl);
    }
}
