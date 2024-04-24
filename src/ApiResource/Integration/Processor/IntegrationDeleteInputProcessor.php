<?php

namespace App\ApiResource\Integration\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Integration;
use App\Entity\Notification;
use App\Entity\WorkerIntegration;
use App\Integration\IntegrationConfigFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class IntegrationDeleteInputProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly IntegrationConfigFactory $integrationConfigFactory,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        /** @var Integration $data */

        $workerIntegrations = $this->em->getRepository(WorkerIntegration::class)->findBy(['integration' => $data]);

        if(!empty($workerIntegrations)) {
            throw new UnprocessableEntityHttpException($this->translator->trans(
                'error.integration.delete.has-workers',
                [],
                'error'
            ));
        }

        $integrationConfig = $this->integrationConfigFactory->getIntegrationConfig($data->getIntegrationType()->getIntegrationCode());
        $integrationConfig->onDelete($data);

        $notifications = $this->em->getRepository(Notification::class)->findBy(['integration' => $data]);
        foreach ($notifications as $notification) {
            $this->em->remove($notification);
        }

        $this->em->remove($data);
        $this->em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}