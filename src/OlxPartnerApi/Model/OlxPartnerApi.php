<?php

namespace App\OlxPartnerApi\Model;

/**
 * This class stores required OLX Partner API endpoints with their parameters and data keys.
 */
enum OlxPartnerApi
{
    case OAuth;
    case GetCategory;
    case ListCategories;

    public function uri() : string
    {
        return match($this)
        {
            self::OAuth => '/open/oauth/token',
            self::GetCategory => '/partner/categories/{category_id}',
            self::ListCategories => '/partner/categories',
        };
    }

    public function method(): string
    {
        return match($this)
        {
            self::OAuth => 'POST',
            self::GetCategory => 'GET',
            self::ListCategories => 'GET',
        };
    }

    public function dataKey(): string
    {
        return match($this)
        {
            self::OAuth => 'access_token',
            self::GetCategory => 'data',
            self::ListCategories => 'data',
        };
    }

    public function headers(string $bearerToken) : array
    {
        return match($this)
        {
            default => [
                'Accept' => 'application/json',
                'Version' => '2.0',
                'Authorization' => 'Bearer ' . $bearerToken,
            ]
        };
    }

}