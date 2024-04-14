<?php

namespace App\ApiResource\User\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\User\Dto\UserUpdateInput;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserUpdateInputProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if(!$data instanceof UserUpdateInput){
            return null;
        }

        $user = $context['data'];

        if(!empty($data->email)){
            $user->setEmail($data->email);
        }

        if(!empty($data->password)){
            $user->setPassword($this->passwordHasher->hashPassword($user, $data->password));
        }

        if(!empty($data->name)){
            $user->setName($data->name);
        }

        return $this->persistProcessor->process($user, $operation, $uriVariables, $context);
    }

}