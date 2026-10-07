<?php

declare(strict_types=1);

namespace App;

use Dotenv\Dotenv;

final class Config
{
    private static ?array $config = null;

    public static function load(): void
    {
        if (self::$config !== null) {
            return;
        }

        $envPath = dirname(__DIR__);
        $dotenv = Dotenv::createImmutable($envPath);
        if (file_exists($envPath . '/.env')) {
            $dotenv->load();
        }

        self::$config = [
            'api_base_url' => $_ENV['SMM_API_BASE_URL'] ?? 'https://api.example.com',
            'api_key' => $_ENV['SMM_API_KEY'] ?? '',
            'api_username' => $_ENV['SMM_API_USERNAME'] ?? '',
            'api_timeout' => (int) ($_ENV['SMM_API_TIMEOUT'] ?? 20),
        ];
    }

    public static function get(string $key): mixed
    {
        self::load();

        return self::$config[$key] ?? null;
    }
}
