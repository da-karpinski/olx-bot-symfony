<?php

namespace App\ApiResource\IntegrationType\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\IntegrationType;
use App\Validator\EntityExists;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class IntegrationTypeCreateInput
{

    #[Groups(['integration-type:view', 'integration-type:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'New integration'])]
    #[Assert\Length(max: 60)]
    #[Assert\NotBlank]
    public string $name;

    #[Groups(['integration-type:view', 'integration-type:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'NEW_INTEGRATION'])]
    #[Assert\Length(max: 60)]
    #[EntityExists(identifier: 'integrationCode', entityClass: IntegrationType::class, shouldExist: false)]
    #[Assert\NotBlank]
    public string $integrationCode;

    #[Groups(['integration-type:view', 'integration-type:write'])]
    #[ApiProperty(openapiContext: ['type' => 'boolean', 'example' => 'true'])]
    #[Assert\NotBlank]
    public bool $enabled;

    #[Groups(['integration-type:view', 'integration-type:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => ['en', 'pl']])]
    #[Assert\NotBlank]
    public array $locales;

}