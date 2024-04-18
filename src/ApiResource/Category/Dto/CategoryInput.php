<?php

namespace App\ApiResource\Category\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Category;
use App\Validator\EntityExists;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryInput
{
    #[Groups(['worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'int', 'example' => 1])]
    #[EntityExists(identifier: 'id', entityClass: Category::class)]
    #[Assert\NotBlank]
    public ?int $id;
}