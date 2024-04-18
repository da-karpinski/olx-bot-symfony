<?php

namespace App\ApiResource\Integration\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Integration\Dto\IntegrationUpdateInput;
use App\Entity\Integration;
use App\Integration\IntegrationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class IntegrationUpdateInputProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly IntegrationFactory $integrationFactory,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if(!$data instanceof IntegrationUpdateInput){
            return null;
        }

        $integration = $this->em->getRepository(Integration::class)->find($uriVariables['id']);

        if(!empty($data->name)) {
            $integration->setName($data->name);
        }

        if(!empty($data->localeCode)) {

            $integrationType = $integration->getIntegrationType();

            $integrationInstance = $this->integrationFactory->getIntegration($integrationType->getIntegrationCode());
            $supportedLocales = $integrationInstance->getEntity()::SUPPORTED_LOCALES;

            if(!in_array($data->localeCode, $supportedLocales)) {
                throw new UnprocessableEntityHttpException($this->translator->trans(
                    'error.integration.locale.not-supported',
                    ['{{ locale }}' => $data->localeCode],
                    'error'
                ));
            }

            $integration->setLocaleCode($data->localeCode);
        }

        return $this->persistProcessor->process($integration, $operation, $uriVariables, $context);
    }
}