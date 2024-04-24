<?php

namespace App\Integration;

use App\ApiResource\Integration\Dto\IntegrationCreateInput;
use App\ApiResource\Integration\Dto\IntegrationUpdateInput;
use App\Entity\Integration;

interface IntegrationConfigInterface
{
    public function getService(): string;

    public function onCreate(IntegrationCreateInput $input, Integration $integration): object;

    public function onUpdate(IntegrationUpdateInput $input, Integration $integration): object;

    public function onDelete(Integration $integration): void;

}