<?php

namespace App\ApiResource\Integration\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Integration\Dto\IntegrationCreateInput;
use App\Entity\Integration;
use App\Entity\IntegrationType;
use App\Entity\User;
use App\Integration\IntegrationConfigFactory;
use App\Integration\IntegrationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class IntegrationCreateInputProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly IntegrationFactory $integrationFactory,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private readonly IntegrationConfigFactory $integrationConfigFactory,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if(!$data instanceof IntegrationCreateInput){
            return null;
        }

        $user = $this->security->getUser();

        if($this->security->isGranted('ROLE_ADMIN') and !empty($data->user->id)) {
            $user = $this->em->getRepository(User::class)->find($data->user->id);
        }

        $integrationType = $this->em->getRepository(IntegrationType::class)->find($data->integrationType->id);

        $integrationInstance = $this->integrationFactory->getIntegration($integrationType->getIntegrationCode());
        $supportedLocales = $integrationInstance->getEntity()::SUPPORTED_LOCALES;

        if(!in_array($data->localeCode, $supportedLocales)) {
            throw new UnprocessableEntityHttpException($this->translator->trans(
                'error.integration.locale.not-supported',
                ['{{ locale }}' => $data->localeCode],
                'error'
            ));
        }

        $integration = new Integration();

        $integration
            ->setName($data->name)
            ->setUser($user)
            ->setIntegrationType($integrationType)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setLocaleCode($data->localeCode);

        $integrationConfigService = $this->integrationConfigFactory->getIntegrationConfig($integrationType->getIntegrationCode());
        $integrationConfig = $integrationConfigService->onCreate($data, $integration);
        $this->em->persist($integrationConfig);

        return $this->persistProcessor->process($integration, $operation, $uriVariables, $context);
    }
}