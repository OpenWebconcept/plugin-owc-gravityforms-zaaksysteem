<?php

namespace OWC\Zaaksysteem\Traits;

use Exception;

trait InformationObject
{
    public function informationObjectToBase64(string $url): string
    {
        try {
            $file = file_get_contents($url, false, $this->streamContext());
        } catch (Exception $e) {
            $file = '';
        }

        return $file ? base64_encode($file) : '';
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

    public function getExtension(string $url): string
    {
        $type = $this->getContentType($url);

        $mimeMap = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/plain' => 'txt',
            'text/csv' => 'csv',
            'text/html' => 'html',
            'application/json' => 'json',
            'application/xml' => 'xml',
        ];

        return $mimeMap[$type] ?? '';
    }

    public function getContentType(string $url): string
    {
        $headers = $this->getHeaders($url);
        $contentType = $headers['content-type'] ?? $headers['Content-Type'] ?? '';

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

        try {
            $response = get_headers($url, 1, $this->streamContext());
        } catch (Exception $e) {
            return [];
        }

        return $response ?: [];
    }

    /**
     * SSL is usually not valid in local environments.
     * Disable verifications.
     */
    protected function streamContext()
    {
        if ('development' !== ($_ENV['APP_ENV'] ?? '')) {
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
