<?php

namespace App\ApiResource\IntegrationType\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\IntegrationType;
use App\Validator\EntityExists;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class IntegrationTypeInput
{
    #[Groups(['integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'int', 'example' => 1])]
    #[EntityExists(identifier: 'id', entityClass: IntegrationType::class)]
    #[Assert\NotBlank]
    public ?int $id;
}