<?php

declare(strict_types=1);

namespace OWC\Zaaksysteem\Clients\Mozart;

use OWC\Zaaksysteem\Contracts\AbstractTokenAuthenticator;
use RuntimeException;

class Authenticator extends AbstractTokenAuthenticator
{
    protected const tokenTransientKey = 'owc_gravityforms_zaaksysteem_jwt_token';
    protected const tokenExpirationFallback = 1800; // 30 minutes

    protected string $clientId;
    protected string $clientSecret;
    protected string $requestTokenUrl;

    public function __construct(string $clientId, string $clientSecret, string $requestTokenUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->requestTokenUrl = $requestTokenUrl;
    }

    public function generateToken(): string
    {
        $cachedToken = get_transient(self::tokenTransientKey);

        if (is_string($cachedToken) && 0 < strlen($cachedToken)) {
            return $cachedToken;
        }

        $response = wp_remote_post($this->requestTokenUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'client_id' => $this->clientId,
                'secret' => $this->clientSecret,
            ]),
        ]);

        if (is_wp_error($response)) {
            throw new RuntimeException('Failed to retrieve JWT token');
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Failed to decode JWT token response');
        }

        $token = $body['authorization'] ?? '';

        if (! is_string($token) || 1 > strlen($token)) {
            throw new RuntimeException('Invalid JWT token received');
        }

        $expiresIn = (int) ($body['expires_in'] ?? 0);

        if (! $expiresIn) {
            $expiresIn = self::tokenExpirationFallback;
        }

        set_transient(self::tokenTransientKey, $token, $expiresIn);

        return $token;
    }
}
