<?php

namespace OWC\Zaaksysteem\Traits;

trait CheckURL
{
    public function checkURL($url): bool
    {
        if (! $this->isValidUrl($url)) {
            return false;
        }

        $response = wp_remote_get($url);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        return true;
    }

    private function isValidUrl($url): bool
    {
        $url = filter_var($url, FILTER_SANITIZE_URL); // Remove invisible characters such as 'soft hyphens'.

        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
