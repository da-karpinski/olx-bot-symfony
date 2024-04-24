<?php

namespace App\ApiResource\Integration\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\ApiResource\IntegrationType\Dto\IntegrationTypeInput;
use App\ApiResource\User\Dto\UserInput;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class IntegrationCreateInput
{
    #[Groups(['integration:view', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'My new integration'])]
    #[Assert\NotBlank]
    public ?string $name;

    #[Groups(['integration:view', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => ['id' => 1]])]
    #[Assert\Valid]
    public ?UserInput $user;

    #[Groups(['integration:view', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => ['id' => 1]])]
    #[Assert\NotBlank]
    #[Assert\Valid]
    public ?IntegrationTypeInput $integrationType;

    #[Groups(['integration:view', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'en'])]
    #[Assert\NotBlank]
    public ?string $localeCode;

    #[Groups(['integration:view', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => [
        'key1' => 'value1',
        'key2' => 'value2'
    ]])]
    #[Assert\NotBlank]
    public ?array $integrationConfig;

}