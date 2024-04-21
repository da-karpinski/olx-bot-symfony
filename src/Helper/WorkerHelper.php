<?php

namespace App\Helper;

use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use App\Entity\CategoryAttribute;
use App\Entity\Integration;
use App\Entity\Worker;
use App\Entity\WorkerIntegration;
use App\OlxPartnerApi\Service\Category\GetCategoryAttributesService;
use App\Validator\CategoryAttributeValidator;
use App\Validator\WorkerIntegrationValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Translation\TranslatorInterface;

class WorkerHelper
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GetCategoryAttributesService $getCategoryAttributesService,
        private readonly TranslatorInterface $translator,
        private readonly WorkerIntegrationValidator $workerIntegrationValidator,
    )
    {
    }

    public function addCategoryAttributes($inputCategoryAttributes, Worker $worker){

        $allowedCategoryAttributes = ($this->getCategoryAttributesService)($worker->getCategory()->getOlxId());

        foreach($inputCategoryAttributes as $inputCategoryAttribute){

            CategoryAttributeValidator::validate($allowedCategoryAttributes, clone $inputCategoryAttribute, $this->translator);

            $categoryAttributeEntity = new CategoryAttribute();
            $categoryAttributeEntity
                ->setWorker($worker)
                ->setAttributeCode($inputCategoryAttribute->attributeCode)
                ->setAttributeValue($inputCategoryAttribute->attributeValue);

            $this->em->persist($categoryAttributeEntity);
            $worker->addCategoryAttribute($categoryAttributeEntity);
        }

    }

    public function addIntegrations($integrations, Worker $worker): void
    {
        $violations = new ConstraintViolationList();

        foreach ($integrations as $key => $integration) {

            $integrationEntity = $this->em->getRepository(Integration::class)->find($integration->id);
            $validationResult = $this->workerIntegrationValidator->belongsToUser($worker, $integrationEntity, $key);

            if($validationResult instanceof ConstraintViolation){
                $violations->add($validationResult);
                continue;
            }

            $workerIntegration = new WorkerIntegration();
            $workerIntegration
                ->setWorker($worker)
                ->setIntegration($integrationEntity);

            $this->em->persist($workerIntegration);
        }

        if(count($violations) > 0){
            throw new ValidationException($violations);
        }

    }

}