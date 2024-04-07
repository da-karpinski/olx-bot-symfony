<?php

namespace App\Integration\Telegram;

use App\Exception\IntegrationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class HttpClient implements TelegramApiInterface
{

    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $integrationLogger //https://symfony.com/doc/current/logging/channels_handlers.html#how-to-autowire-logger-channels
    ){

    }

    public function getClient() : HttpClientInterface
    {
        return $this->client;
    }

    public function getResponseCode(mixed $response) : int
    {
        return $response->getStatusCode();
    }

    public function getResponseBody(mixed $response) : array|string|null
    {
        return $response->getContent(false);
    }

    public function getResponseContentType(mixed $response) : array|string|null
    {
        return $response->getHeaders()['content-type'][0];
    }

    public function isResponseValid(ResponseInterface $response) : bool
    {
        return $response->getStatusCode() === Response::HTTP_OK and json_decode($this->getResponseBody($response),true)["ok"];
    }

    public function request(string $method, string $uri, mixed $data) : mixed
    {

        try {
            $response = $this->client->request($method, $uri, $data);
        } catch (Throwable $e) {

            throw new IntegrationException(
                $e->getMessage(),
                $e->getCode(),
                $this->integrationLogger
            );
        }

        $responseBody = $this->getResponseBody($response);

        if (empty($responseBody)) {

            throw new IntegrationException(
                '[Telegram Bot API] The response body is empty',
                $this->getResponseCode($response),
                $this->integrationLogger
            );
        }

        if (!$responseBody = json_decode($responseBody, true)) {

            throw new IntegrationException(
                '[Telegram Bot API] API returned unsupported response body',
                $this->getResponseCode($response),
                $this->integrationLogger
            );
        }

        if (!$this->isResponseValid($response)) {

            throw new IntegrationException(
                sprintf("[Telegram Bot API] [%s]: %s.",
                    $response->getStatusCode(),
                    $response->getContent(),
                ),
                $this->getResponseCode($response),
                $this->integrationLogger
            );
        }

        return $responseBody;
    }
}