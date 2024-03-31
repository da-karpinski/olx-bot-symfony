<?php

namespace App\OlxPublicApi\Exception;

use Psr\Log\LoggerInterface;
use Stringable;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OlxPublicApiException extends \Exception
{
    public function __construct(
        protected $message,
        protected $code,
        private LoggerInterface $logger,
        private mixed $requestData = null,
        private ?string $uri = null,
        private ?Throwable $previous = null,
    )
    {
        parent::__construct($message, $code, $previous);

        $this->logException();
    }

    public function logException() {

        $message = strval($this);

        match($this->code) {
            Response::HTTP_BAD_REQUEST => $this->logger->error($message),
            Response::HTTP_INTERNAL_SERVER_ERROR => $this->logger->error($message),
            default => $this->logger->error($message)
        };
    }

    public function __toString() {

        $stringifiedRequestData = $this->requestData instanceof Stringable ? strval($this->requestData) : json_encode($this->requestData);

        return __CLASS__ . ": [{$this->code}]: {$this->message}. URI: {$this->uri}. Request data: {$stringifiedRequestData} \n";
    }

}