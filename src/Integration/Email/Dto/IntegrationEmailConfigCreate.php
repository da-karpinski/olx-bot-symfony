<?php

namespace App\Integration\Email\Dto;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class IntegrationEmailConfigCreate
{
    #[Groups(['integration:view', 'integration:list', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'j.doe@example.com'])]
    #[Assert\Email]
    #[Assert\NotBlank]
    public ?string $recipientAddress = null;

    #[Groups(['integration:view', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => [
        'a.kowalska@exmaple.com',
        'j.kowalski@example.com'
    ]])]
    public ?array $ccAddresses = null;

    #[Groups(['integration:view', 'integration:write'])]
    #[ApiProperty(openapiContext: ['type' => 'array', 'example' => [
        'b.nowak@exmaple.com',
        'z.nowak@example.com'
    ]])]
    public ?array $bccAddresses = null;

}