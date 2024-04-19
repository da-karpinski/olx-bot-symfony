<?php

namespace App\ApiResource\Integration\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Integration;
use App\Validator\EntityExists;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class IntegrationInput
{
    #[Groups(['worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'int', 'example' => 1])]
    #[EntityExists(identifier: 'id', entityClass: Integration::class)]
    #[Assert\NotBlank]
    public ?int $id;
}