<?php

namespace App\Integration;

class IntegrationFactory
{
    /**
     * @var IntegrationInterface[]
     */
    private $integrations;

    public function __construct(iterable $integrations)
    {
        $this->integrations = $integrations;
    }

    public function getIntegration(string $name): ?IntegrationInterface
    {
        foreach ($this->integrations as $integration) {
            if ($integration::INTEGRATION_CODE === $name) {
                return $integration;
            }
        }

        return null;
    }
}