<?php

namespace App\ApiResource\Worker\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Worker\Dto\WorkerUpdateInput;
use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Entity\City;
use App\Entity\Worker;
use App\Entity\WorkerIntegration;
use App\Helper\WorkerHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class WorkerUpdateInputProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private readonly WorkerHelper $workerHelper
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if(!$data instanceof WorkerUpdateInput) {
            return null;
        }

        $worker = $this->em->getRepository(Worker::class)->find($uriVariables['id']);

        if(!empty($data->name)) {
            $worker->setName($data->name);
        }

        if(!empty($data->city->id)) {
            $city = $this->em->getRepository(City::class)->find($data->city->id);
            $worker->setCity($city);
        }

        if(!empty($data->category->id)) {

            if($worker->getCategory()->getId() !== $data->category->id){
                $this->clearCategoryAttributes($worker);
            }
            $category = $this->em->getRepository(Category::class)->find($data->category->id);
            $worker->setCategory($category);
        }

        if(!is_null($data->enabled)) {
            $worker->setEnabled($data->enabled);
        }

        if(!empty($data->executionInterval)) {
            $worker->setExecutionInterval($data->executionInterval);
        }

        if(!is_null($data->categoryAttributes)) {
            $this->clearCategoryAttributes($worker);

            if(!empty($data->category->id)){
                $this->workerHelper->addCategoryAttributes($data->categoryAttributes, $worker);
            }
        }

        if(!is_null($data->integrations)) {
            $this->clearIntegrations($worker);
            $this->workerHelper->addIntegrations($data->integrations, $worker);
        }

        //TODO: prefetch offers

        return $this->persistProcessor->process($worker, $operation, $uriVariables, $context);

    }

    private function clearCategoryAttributes(Worker $worker): void
    {
        $categoryAttributes = $this->em->getRepository(CategoryAttribute::class)->findBy(['worker' => $worker]);
        foreach($categoryAttributes as $categoryAttribute) {
            $this->em->remove($categoryAttribute);
        }
    }

    private function clearIntegrations(Worker $worker): void
    {
        $integrations = $this->em->getRepository(WorkerIntegration::class)->findBy(['worker' => $worker]);
        foreach($integrations as $integration) {
            $this->em->remove($integration);
        }
    }

}