<?php

namespace App\ApiResource\User\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\User;
use App\Validator\EntityExists;
use App\Validator\PasswordIsStrong;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateInput
{
    #[Groups(['user:view', 'user:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'j.doe@example.com'])]
    #[Assert\Email]
    #[EntityExists(identifier: 'email', entityClass: User::class, shouldExist: false)]
    public string $email;

    #[Groups(['user:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'John123!'])]
    #[PasswordIsStrong]
    public string $password;

    #[Groups(['user:view', 'user:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'John Doe'])]
    public string $name;

}