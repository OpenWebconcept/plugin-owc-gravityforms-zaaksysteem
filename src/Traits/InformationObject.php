<?php

namespace OWC\Zaaksysteem\Traits;

trait InformationObject
{
    public function informationObjectToBase64(string $url): string
    {
        $file = file_get_contents($url, false, $this->streamContext());

        return $file ? base64_encode($file) : '';
    }

    /**
     * Format the title based on a URL.
     * Replaces soft hyphens on the go.
     */
    public function getInformationObjectTitle(string $url = ''): string
    {
        $basename = htmlentities(basename($url));

        return str_replace('&shy;', '-', $basename);
    }

    public function getContentLength(string $url): string
    {
        $headers = $this->getHeaders($url);
        $contentLength = $headers['Content-Length'] ?? '';

        if (is_array($contentLength) && ! empty($contentLength[0])) {
            return $contentLength[0];
        }

        return $contentLength ?: '';
    }

    public function getContentType(string $url): string
    {
        $headers = $this->getHeaders($url);
        $contentType = $headers['Content-Type'] ?? '';

        if (is_array($contentType) && ! empty($contentType[0])) {
            return $contentType[0];
        }

        return $contentType ?: '';
    }

    protected function getHeaders(string $url): array
    {
        if (empty($url)) {
            return [];
        }

        $response = get_headers($url, 1, $this->streamContext());

        return $response ?: [];
    }

    /**
     * SSL is usually not valid in local environments.
     * Disable verifications.
     */
    protected function streamContext()
    {
        if (($_ENV['APP_ENV'] ?? '') !== 'development') {
            return null;
        }

        return stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
    }
}
