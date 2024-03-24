<?php

namespace App\OlxPartnerApi\Adapter;

class OAuthAdapter
{

    public static function adapt(string $clientId, string $clientSecret)
    {
        return [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'v2 read',
        ];
    }

}