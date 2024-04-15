<?php

namespace App\ApiResource\IntegrationType\Dto;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class IntegrationTypeUpdateInput
{
    #[Groups(['integration-type:view', 'integration-type:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'New integration'])]
    #[Assert\Length(max: 60)]
    public ?string $name = null;

    #[Groups(['integration-type:view', 'integration-type:write'])]
    #[ApiProperty(openapiContext: ['type' => 'boolean', 'example' => 'true'])]
    public ?bool $enabled = null;

    #[Groups(['integration-type:view', 'integration-type:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => ['en', 'pl']])]
    public ?array $locales = null;

}