<?php

namespace App\ApiResource\City\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\City;
use App\Validator\EntityExists;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CityInput
{
    #[Groups(['worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'int', 'example' => 1])]
    #[EntityExists(identifier: 'id', entityClass: City::class)]
    #[Assert\NotBlank]
    public ?int $id;
}