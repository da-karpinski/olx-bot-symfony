<?php

namespace App\Integration;

class IntegrationConfigFactory
{
    /**
     * @var IntegrationConfigInterface[]
     */
    private $integrationConfigs;

    public function __construct(iterable $integrationConfigs)
    {
        $this->integrationConfigs = $integrationConfigs;
    }

    public function getIntegrationConfig(string $name): ?IntegrationConfigInterface
    {
        foreach ($this->integrationConfigs as $integrationConfig) {
            if ($integrationConfig->getService()::INTEGRATION_CODE === $name) {
                return $integrationConfig;
            }
        }

        return null;
    }
}