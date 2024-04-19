<?php

namespace App\ApiResource\Worker\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Worker\Dto\WorkerCreateInput;
use App\Entity\Category;
use App\Entity\City;
use App\Entity\User;
use App\Entity\Worker;
use App\Helper\WorkerHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class WorkerCreateInputProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private readonly WorkerHelper $workerHelper
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {

        if(!$data instanceof WorkerCreateInput) {
            return null;
        }

        $user = $this->security->getUser();

        if($this->security->isGranted('ROLE_ADMIN') and !empty($data->user->id)) {
            $user = $this->em->getRepository(User::class)->find($data->user->id);
        }

        $city = $this->em->getRepository(City::class)->find($data->city->id);
        $category = $this->em->getRepository(Category::class)->find($data->category->id);

        $worker = new Worker();
        $worker
            ->setName($data->name)
            ->setUser($user)
            ->setCity($city)
            ->setCategory($category)
            ->setEnabled($data->enabled)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setExecutionInterval($data->executionInterval);

        $this->workerHelper->addCategoryAttributes($data->categoryAttributes, $worker);
        $this->workerHelper->addIntegrations($data->integrations, $worker);
        //TODO: prefetch offers

        return $this->persistProcessor->process($worker, $operation, $uriVariables, $context);

    }

}