<?php

namespace App\OlxPublicApi;

use App\OlxPublicApi\Exception\OlxPublicApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class HttpClient implements OlxPublicApiInterface
{

    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $olxPublicApiLogger //https://symfony.com/doc/current/logging/channels_handlers.html#how-to-autowire-logger-channels
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
        return in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_NOT_FOUND, Response::HTTP_GONE]);
    }

    public function request(string $method, string $uri, mixed $data) : mixed
    {

        try {
            $response = $this->client->request($method, $uri, $data);
        } catch (Throwable $e) {

            throw new OlxPublicApiException(
                $e->getMessage(),
                $e->getCode(),
                $this->olxPublicApiLogger,
                $data,
                $uri
            );
        }

        $responseBody = $this->getResponseBody($response);

        if (empty($responseBody)) {

            throw new OlxPublicApiException(
                '[OLX Public API] The response body is empty',
                $this->getResponseCode($response),
                $this->olxPublicApiLogger,
                $data,
                $uri
            );
        }

        if (!$responseBody = json_decode($responseBody, true)) {

            if($response->getStatusCode() !== Response::HTTP_GONE){
                throw new OlxPublicApiException(
                    '[OLX Public API] API returned unsupported response body',
                    $this->getResponseCode($response),
                    $this->olxPublicApiLogger,
                    $data,
                    $uri
                );
            }
        }

        if (!$this->isResponseValid($response)) {

            $errorDetail = $responseBody["error"]["detail"];

            if(isset($responseBody["error"]["validation"])){
                $errorDetail .= ". Validation details: " . serialize($responseBody["error"]["validation"]);
            }

            throw new OlxPublicApiException(
                sprintf("[OLX Public API] [%s]: %s. %s",
                    $response->getStatusCode(),
                    $responseBody["error"]["title"],
                    $errorDetail
                ),
                $this->getResponseCode($response),
                $this->olxPublicApiLogger,
                $data,
                $uri
            );
        }

        return $responseBody;
    }
}