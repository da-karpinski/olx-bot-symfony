<?php

namespace App\ApiResource\Integration\Dto;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

class IntegrationUpdateInput
{
    #[Groups(['integration:view', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'My new integration'])]
    public ?string $name = null;

    #[Groups(['integration:view', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'en'])]
    public ?string $localeCode = null;

}