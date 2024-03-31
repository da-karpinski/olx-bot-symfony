<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Entity\City;
use App\Entity\User;
use App\Entity\Worker;
use App\Payload\Request\WorkerRequestPayload;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WorkerService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    )
    {
    }

    public function createWorker(WorkerRequestPayload $payload): Worker
    {
        $worker = new Worker();

        if($payload->getUser() !== null) {
            if($this->security->isGranted('ROLE_ADMIN')){
                $worker->setUser($this->em->getRepository(User::class)->find($payload->getUser()));
            }else{
                throw new AccessDeniedHttpException('api.worker.create.insufficient_permissions');
            }
        }else{
            $worker->setUser($this->security->getUser());
        }

        $worker->setCity($this->em->getRepository(City::class)->find($payload->getCity()));

        if($payload->getSecondSubcategory() !== null){
            $worker->setCategory($this->em->getRepository(Category::class)->find($payload->getSecondSubcategory()));
        }else{
            $worker->setCategory($this->em->getRepository(Category::class)->find($payload->getFirstSubcategory()));
        }

        $worker->setCreatedAt(new \DateTimeImmutable());
        $worker->setExecutionInterval($payload->getExecutionInterval());
        $worker->setEnabled($payload->getIsEnabled() ?? true);

        $this->em->persist($worker);

        if(!empty($payload->getAttributes())){
            $this->createAttributes($worker, $payload->getAttributes());
        }

        $this->em->flush();

        return $worker;
    }

    private function createAttributes(Worker $worker, array $attributes): void
    {
        foreach($attributes as $key => $value){
            $categoryAttribute = new CategoryAttribute();
            $categoryAttribute->setWorker($worker);
            $categoryAttribute->setAttributeCode($key);
            $categoryAttribute->setAttributeValue($value);

            $this->em->persist($categoryAttribute);
        }
    }

}