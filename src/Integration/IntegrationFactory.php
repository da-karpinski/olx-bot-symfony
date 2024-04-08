<?php

namespace App\Integration;

use App\Integration\Email\Service\IntegrationEmailService;
use App\Integration\Telegram\Service\IntegrationTelegramService;

class IntegrationFactory
{

    public function __construct(
        private readonly IntegrationTelegramService $integrationTelegramService,
        private readonly IntegrationEmailService $integrationEmailService,
    )
    {
    }

    public function getIntegration(string $integrationCode): IntegrationInterface
    {
        $class = match ($integrationCode) {
            IntegrationEmailService::INTEGRATION_CODE => $this->integrationEmailService,
            IntegrationTelegramService::INTEGRATION_CODE => $this->integrationTelegramService,
            default => null
        };

        if ($class) {
            return $class;
        }

        throw new \RuntimeException('Integration not found');
    }
}