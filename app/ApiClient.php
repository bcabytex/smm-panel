<?php

declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class ApiClient
{
    private Client $http;
    private string $baseUrl;
    private string $apiKey;
    private string $username;

    public function __construct()
    {
        Config::load();

        $this->baseUrl = rtrim((string) Config::get('api_base_url'), '/');
        $this->apiKey = (string) Config::get('api_key');
        $this->username = (string) Config::get('api_username');

        $this->http = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => (int) Config::get('api_timeout'),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Fetch all available services from the provider API.
     */
    public function getServices(): array
    {
        try {
            $response = $this->http->get('/api/services', [
                'query' => ['key' => $this->apiKey, 'username' => $this->username],
            ]);

            $payload = json_decode((string) $response->getBody(), true);
            return is_array($payload) ? $payload : [];
        } catch (GuzzleException | \Throwable $e) {
            return $this->getFallbackServices();
        }
    }

    /**
     * Place a new order.
     */
    public function createOrder(array $order): array
    {
        try {
            $response = $this->http->post('/api/orders', [
                'json' => [
                    'key' => $this->apiKey,
                    'username' => $this->username,
                    'service' => $order['service'] ?? '',
                    'link' => $order['link'] ?? '',
                    'quantity' => (int) ($order['quantity'] ?? 0),
                    'comments' => $order['comments'] ?? '',
                ],
            ]);

            $payload = json_decode((string) $response->getBody(), true);
            return is_array($payload) ? $payload : ['status' => 'error', 'message' => 'Malformed response'];
        } catch (GuzzleException | \Throwable $e) {
            return [
                'status' => 'error',
                'message' => 'Unable to connect to the SMM API. Please check your API settings.',
            ];
        }
    }

    private function getFallbackServices(): array
    {
        return [
            ['id' => 1, 'name' => 'Instagram Followers', 'price' => 2.50, 'min' => 100, 'max' => 100000],
            ['id' => 2, 'name' => 'YouTube Views', 'price' => 0.90, 'min' => 250, 'max' => 500000],
            ['id' => 3, 'name' => 'TikTok Likes', 'price' => 1.40, 'min' => 200, 'max' => 250000],
            ['id' => 4, 'name' => 'Facebook Page Likes', 'price' => 2.10, 'min' => 100, 'max' => 50000],
            ['id' => 5, 'name' => 'Twitter Followers', 'price' => 1.80, 'min' => 100, 'max' => 200000],
        ];
    }
}
