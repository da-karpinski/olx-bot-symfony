<?php

namespace App\Exception;

use Psr\Log\LoggerInterface;
use Throwable;

class IntegrationException extends \Exception
{
    public function __construct(
        protected $message,
        protected $code,
        private LoggerInterface $logger,
        private ?Throwable $previous = null,
    )
    {
        parent::__construct($message, $code, $previous);
        $this->logException();
    }

    public function logException() {

        $this->logger->log($this->code, strval($this));
    }

    public function __toString() {

         return sprintf(
            "%s at line %d: %s.\n\nTrace: %s",
            $this->getFile(),
            $this->getLine(),
            $this->message,
            $this->getTraceAsString()
        );
    }

}