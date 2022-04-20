<?php declare(strict_types=1);

namespace OWC\OpenZaak\Repositories;

use Firebase\JWT\JWT;

class BaseRepository
{
    public function __construct()
    {
        $this->url = $_ENV['OPEN_ZAAK_URL'];
        $this->clientID = $_ENV['OPEN_ZAAK_CLIENT_ID'];
        $this->clientSecret = $_ENV['OPEN_ZAAK_CLIENT_SECRET'];
    }

    public function request(string $url = ''): ?array
    {
        if (empty($url)) {
            $url = $this->url;
        }
        
        $request = \wp_remote_get($url, [
            'headers' => [
                'Accept-Crs' => 'EPSG:4326',
                'Authorization' => sprintf('Bearer %s', $this->generateToken())
            ]
        ]);

        if (\is_wp_error($request) || 200 !== $request['response']['code']) {
            return null;
        }

        $body = json_decode($request['body'], true);
        
        if (json_last_error() !== JSON_ERROR_NONE || empty($body['results'])) {
            return null;
        }

        return $body['results'] ?? [];
    }

    protected function generateToken(): string
    {
        $payload = [
            'iss' => $this->clientID,
            'iat' => time(),
            'client_id' => $this->clientID,
            'user_id' => '8d2646a6-0404-450b-b2b3-0fc64eadccab',
            'user_representation' => '8d2646a6-0404-450b-b2b3-0fc64eadccab',
        ];

        return JWT::encode($payload, $this->clientSecret, 'HS256');
    }
}
