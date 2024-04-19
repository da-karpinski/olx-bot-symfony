<?php

namespace App\Validator;

use App\Entity\Integration;
use App\Entity\Worker;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Contracts\Translation\TranslatorInterface;

class WorkerIntegrationValidator
{

    public function __construct(
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function belongsToUser(Worker $worker, Integration $integration, ?int $index = null): ConstraintViolation|true
    {
        if($integration->getUser() !== $worker->getUser()){

            if($index === null){
                $propertyPath = 'integration';
            }else{
                $propertyPath = 'integrations['.$index.']';
            }
            return new ConstraintViolation(
                $this->translator->trans('error.integration.user.different-user', [], 'error'),
                '', [], null, $propertyPath, '', null
            );
        }
        return true;
    }

}