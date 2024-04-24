<?php

namespace App\ApiResource\Integration\Provider;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Integration;
use App\Integration\IntegrationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class IntegrationOutputProvider implements ProviderInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly IntegrationFactory $integrationFactory,
        private readonly Security $security,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if($operation instanceof GetCollection) {
            return $this->provideCollection();
        }else{
            $integration = $this->em->getRepository(Integration::class)->find($uriVariables['id']);
            return $this->provideItem($integration);
        }
    }

    private function provideCollection(): array
    {

        if($this->security->isGranted('ROLE_ADMIN')) {
            $integrations = $this->em->getRepository(Integration::class)->findAll();
        }else if($this->security->isGranted('ROLE_USER')){
            $integrations = $this->em->getRepository(Integration::class)->findBy(['user' => $this->security->getUser()]);
        }else{
            $integrations = [];
        }

        $output = [];

        foreach ($integrations as $integration) {
            $output[] = $this->provideItem($integration);
        }

        return $output;
    }

    private function provideItem(Integration $integration): Integration
    {
        $integrationConfigEntity = $this->integrationFactory->getIntegration($integration->getIntegrationType()->getIntegrationCode());
        $integration->setIntegrationConfig($this->em->getRepository($integrationConfigEntity->getEntity())->findOneBy(['integration' => $integration]));

        return $integration;
    }

}