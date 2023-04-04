<?php

namespace OWC\Zaaksysteem\Traits;

trait InformationObject
{
    public function informationObjectToBase64(string $url): string
    {
        $file = file_get_contents($url, false, $this->streamContext());

        return $file ? base64_encode($file) : '';
    }

    public function getInformationObjectTitle(string $url = ''): string
    {
        return basename($url);
    }

    public function getContentLength(string $url): string
    {
        $headers = $this->getHeaders($url);
        $contentLength = $headers['Content-Length'] ?? '0';

        return $contentLength ?: '';
    }

    public function getContentType(string $url): string
    {
        $headers = $this->getHeaders($url);
        $contentLength = $headers['Content-Type'] ?? '0';

        return $contentLength ?: '';
    }

    protected function getHeaders(string $url): array
    {
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
