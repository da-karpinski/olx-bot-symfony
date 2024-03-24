<?php

namespace App\OlxPartnerApi;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface OlxPartnerApiInterface
{
    public function getResponseCode(mixed $response) : int;

    public function getResponseBody(mixed $response) : array|string|null;

    public function getResponseContentType(mixed $response) : array|string|null;

    public function isResponseValid(ResponseInterface $response) : bool;

    public function request(string $method, string $uri, mixed $data) : mixed;
}