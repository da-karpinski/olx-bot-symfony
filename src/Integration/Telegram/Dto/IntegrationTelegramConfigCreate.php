<?php

namespace App\Integration\Telegram\Dto;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class IntegrationTelegramConfigCreate
{
    #[Groups(['integration:view', 'integration:list', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => '1234-5678'])]
    #[Assert\NotBlank]
    public ?string $otpCode = null;

}