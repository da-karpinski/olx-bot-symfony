<?php

namespace App\ApiResource\IntegrationType\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\IntegrationType\Dto\IntegrationTypeUpdateInput;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class IntegrationTypeUpdateInputProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if(!$data instanceof IntegrationTypeUpdateInput){
            return null;
        }

        $integrationType = $context['data'];

        if(!empty($data->name)){
            $integrationType->setName($data->name);
        }

        if(!is_null($data->enabled)){
            $integrationType->setEnabled($data->enabled);
        }

        if(!empty($data->locales)){
            $integrationType->setLocales($data->locales);
        }

        return $this->persistProcessor->process($integrationType, $operation, $uriVariables, $context);
    }

}