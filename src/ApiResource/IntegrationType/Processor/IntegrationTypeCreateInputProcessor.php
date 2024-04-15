<?php

namespace App\ApiResource\IntegrationType\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\IntegrationType\Dto\IntegrationTypeCreateInput;
use App\Entity\IntegrationType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class IntegrationTypeCreateInputProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if(!$data instanceof IntegrationTypeCreateInput){
            return null;
        }

        $integrationType = new IntegrationType();
        $integrationType->setName($data->name);
        $integrationType->setIntegrationCode($data->integrationCode);
        $integrationType->setEnabled($data->enabled);
        $integrationType->setLocales($data->locales);


        return $this->persistProcessor->process($integrationType, $operation, $uriVariables, $context);
    }

}