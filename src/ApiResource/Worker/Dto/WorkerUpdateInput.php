<?php

namespace App\ApiResource\Worker\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\ApiResource\Category\Dto\CategoryInput;
use App\ApiResource\CategoryAttribute\Dto\CategoryAttributeCreateInput;
use App\ApiResource\City\Dto\CityInput;
use App\ApiResource\Integration\Dto\IntegrationInput;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class WorkerUpdateInput
{
    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'My new worker'])]
    public ?string $name = null;

    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => ['id' => 1]])]
    #[Assert\Valid]
    public ?CityInput $city = null;

    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => ['id' => 1]])]
    #[Assert\Valid]
    public ?CategoryInput $category = null;

    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'boolean', 'example' => true])]
    public ?bool $enabled = null;

    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'int', 'example' => 5])]
    #[Assert\GreaterThanOrEqual(3)]
    #[Assert\LessThanOrEqual(1440)]
    public ?int $executionInterval = null;

    /** @var CategoryAttributeCreateInput[] */
    #[Groups(['worker:view', 'worker:write'])]
    #[Assert\Valid]
    public ?array $categoryAttributes = null;

    /** @var IntegrationInput[] */
    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => [['id' => 1], ['id' => 2]]])]
    #[Assert\Valid]
    public ?array $integrations = null;

}