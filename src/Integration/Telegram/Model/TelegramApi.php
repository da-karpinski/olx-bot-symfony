<?php

namespace App\Integration\Telegram\Model;

/**
 * This class stores required Telegram Bot API endpoints with their parameters and data keys.
 */
enum TelegramApi
{
    case sendMessage;
    case setWebHook;

    public function uri() : string
    {
        $uri = "/bot{token}";
        return match($this)
        {
            self::sendMessage => $uri . '/sendMessage',
            self::setWebHook => $uri . '/setWebhook',
        };
    }

    public function method(): string
    {
        return match($this)
        {
            self::sendMessage => 'POST',
            self::setWebHook => 'POST',
        };
    }

    public function dataKey(): string
    {
        return match($this)
        {
            self::sendMessage => 'ok',
            self::setWebHook => 'ok',
        };
    }

    public function headers() : array
    {
        return match($this)
        {
            default => [
                'Accept' => 'application/json',
            ]
        };
    }

}