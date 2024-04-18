<?php

namespace App\ApiResource\CategoryAttribute\Dto;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryAttributeCreateInput
{
    #[Groups(['worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'price:from'])]
    #[Assert\NotBlank]
    public ?string $attributeCode;

    #[Groups(['worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => '100'])]
    #[Assert\NotBlank]
    public ?string $attributeValue;

}