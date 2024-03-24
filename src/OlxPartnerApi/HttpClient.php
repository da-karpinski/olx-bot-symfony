<?php

namespace App\OlxPartnerApi;

use App\OlxPartnerApi\Exception\OlxPartnerApiException;
use App\OlxPartnerApi\Model\OlxPartnerApiErrorModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class HttpClient implements OlxPartnerApiInterface
{

    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $olxPartnerApiLogger //https://symfony.com/doc/current/logging/channels_handlers.html#how-to-autowire-logger-channels
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
        return $response->getStatusCode() === Response::HTTP_OK and $this->getResponseContentType($response) === 'application/json';
    }

    public function request(string $method, string $uri, mixed $data) : mixed
    {

        try {
            $response = $this->client->request($method, $uri, $data);
        } catch (Throwable $e) {

            throw new OlxPartnerApiException(
                $e->getMessage(),
                $e->getCode(),
                $this->olxPartnerApiLogger,
                $data,
                $uri
            );
        }

        $responseBody = $this->getResponseBody($response);

        if (empty($responseBody)) {

            throw new OlxPartnerApiException(
                '[OLX Partner API] The response body is empty',
                $this->getResponseCode($response),
                $this->olxPartnerApiLogger,
                $data,
                $uri
            );
        }

        if (!$responseBody = json_decode($responseBody, true)) {

            throw new OlxPartnerApiException(
                '[OLX Partner API] API returned unsupported response body',
                $this->getResponseCode($response),
                $this->olxPartnerApiLogger,
                $data,
                $uri
            );
        }

        if (Response::HTTP_OK !== $this->getResponseCode($response)) {

            throw new OlxPartnerApiException(
                sprintf("[OLX Partner API] [%s]: %s. %s",
                    $response->getStatusCode(),
                    OlxPartnerApiErrorModel::getErrorTitle($responseBody),
                    OlxPartnerApiErrorModel::getErrorDetail($responseBody)
                ),
                $this->getResponseCode($response),
                $this->olxPartnerApiLogger,
                $data,
                $uri
            );
        }

        return $responseBody;
    }
}