<?php

namespace App\ApiResource\User\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\User;
use App\Validator\EntityExists;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserInput
{
    #[Groups(['integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'int', 'example' => 1])]
    #[EntityExists(identifier: 'id', entityClass: User::class)]
    #[Assert\NotBlank]
    public ?int $id;
}