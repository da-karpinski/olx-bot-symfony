<?php

namespace App\ApiResource\User\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\User;
use App\Validator\EntityExists;
use App\Validator\PasswordIsStrong;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateInput
{

    #[Groups(['user:view', 'user:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'j.doe@example.com'])]
    #[Assert\Email]
    #[EntityExists(identifier: 'email', entityClass: User::class, shouldExist: false)]
    public string $email;

    #[Groups(['user:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'John123!'])]
    #[PasswordIsStrong]
    #[Assert\NotBlank]
    public string $password;

    #[Groups(['user:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'John123!'])]
    #[Assert\NotBlank]
    #[Assert\EqualTo(propertyPath: 'password', options: ['message' => 'user.password.not-the-same'])]
    public string $repeatPassword;

    #[Groups(['user:view', 'user:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'John Doe'])]
    #[Assert\NotBlank]
    public string $name;

    #[Groups(['user:view', 'user:write'])]
    #[ApiProperty(openapiContext: ['type' => 'string', 'example' => 'ROLE_USER'])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: User::ROLES, message: 'user.role.not-found')]
    public string $role;

}