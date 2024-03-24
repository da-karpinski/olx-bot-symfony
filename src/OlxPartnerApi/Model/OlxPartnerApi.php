<?php

namespace App\OlxPartnerApi\Model;

/**
 * This class stores all possible OLX Partner API endpoints with their parameters and data keys.
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
            self::GetCategory => '/categories/{category_id}',
            self::ListCategories => '/categories',
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
            self::GetCategory => '',
            self::ListCategories => '',
        };
    }

    public function totalItemsField(): string
    {
        return match($this)
        {
            default => "total_count"
        };
    }

    public function itemsPerPageField(): string
    {
        return match($this)
        {
            default => "page_count"
        };
    }

    public function pageField(): string
    {
        return match($this)
        {
            default => "page_nr"
        };
    }

    public function totalPageField(): string
    {
        return match($this)
        {
            default => "total_pages"
        };
    }

    public function headers(string $bearerToken) : array
    {
        return match($this)
        {
            default => [
                'Accept' => 'application/json',
                'Authentication' => 'Bearer ' . $bearerToken,
            ]
        };
    }

}