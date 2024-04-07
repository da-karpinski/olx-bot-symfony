<?php

namespace App\Integration\Telegram\Model;

/**
 * This class stores required Telegram Bot API endpoints with their parameters and data keys.
 */
enum TelegramApi
{
    case sendMessage;

    public function uri() : string
    {
        $uri = "/bot{token}";
        return match($this)
        {
            self::sendMessage => $uri . '/sendMessage',
        };
    }

    public function method(): string
    {
        return match($this)
        {
            self::sendMessage => 'POST',
        };
    }

    public function dataKey(): string
    {
        return match($this)
        {
            self::sendMessage => 'ok',
        };
    }

    public function headers(string $bearerToken) : array
    {
        return match($this)
        {
            default => [
                'Accept' => 'application/json',
            ]
        };
    }

}