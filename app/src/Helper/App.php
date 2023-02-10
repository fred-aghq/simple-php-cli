<?php

namespace App\Helper;

class App
{
    public static function env(): string
    {
        return getenv('APP_ENV');
    }
    public static function isEnv(string $env): bool
    {
        return self::env() === $env;
    }
    public static function isProd(): bool
    {
        return self::isEnv('production');
    }

    public static function isDev(): bool
    {
        return self::isEnv('dev');
    }
}
