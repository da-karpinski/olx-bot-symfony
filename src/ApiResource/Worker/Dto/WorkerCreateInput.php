<?php

namespace App\ApiResource\Worker\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\ApiResource\Category\Dto\CategoryInput;
use App\ApiResource\CategoryAttribute\Dto\CategoryAttributeCreateInput;
use App\ApiResource\City\Dto\CityInput;
use App\ApiResource\Integration\Dto\IntegrationInput;
use App\ApiResource\User\Dto\UserInput;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class WorkerCreateInput
{
    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'My new worker'])]
    #[Assert\NotBlank]
    public string $name;

    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => ['id' => 1]])]
    #[Assert\Valid]
    public UserInput $user;

    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => ['id' => 1]])]
    #[Assert\Valid]
    public CityInput $city;

    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => ['id' => 1]])]
    #[Assert\Valid]
    public CategoryInput $category;

    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'boolean', 'example' => true])]
    #[Assert\NotBlank]
    public bool $enabled;

    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'int', 'example' => 5])]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(3)]
    #[Assert\LessThanOrEqual(1440)]
    public int $executionInterval;

    /** @var CategoryAttributeCreateInput[] */
    #[Groups(['worker:view', 'worker:write'])]
    #[Assert\Valid]
    public array $categoryAttributes;

    /** @var IntegrationInput[] */
    #[Groups(['worker:view', 'worker:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => [['id' => 1], ['id' => 2]]])]
    #[Assert\Valid]
    public array $integrations;

}