<?php

namespace App\Service;

use App\Entity\Offer;
use App\Entity\Worker;
use App\Integration\IntegrationInterface;
use App\OlxPublicApi\Adapter\OfferParameterToEntityAdapter;
use App\OlxPublicApi\Adapter\OfferToEntityAdapter;
use App\OlxPublicApi\Service\GetOffersForWorkerService;
use Doctrine\ORM\EntityManagerInterface;

class OfferService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GetOffersForWorkerService $getOffersForWorkerService,
        private readonly IntegrationInterface $integration
    )
    {
    }

    public function processOffersForWorker(Worker $worker): array
    {
        $results = [
            'new' => 0,
            'updated' => 0,
        ];

        $offers = ($this->getOffersForWorkerService)($worker);

        if(empty($offers)){
            return $results;
        }

        $newOffers = [];

        foreach ($offers as $olxOffer) {

            $offer = OfferToEntityAdapter::adapt($olxOffer, $worker);

            if($existingOffer = $this->em->getRepository(Offer::class)->findOneBy(['olxId' => $offer->getOlxId(), 'worker' => $worker])){
                $existingOffer->setLastSeenAt(new \DateTimeImmutable());
                $this->em->persist($existingOffer);
                $results['updated']++;
            }else{
                $this->em->persist($offer);
                foreach ($olxOffer['params'] as $param) {
                    if($param['key'] !== 'price') {
                        $offerParameter = OfferParameterToEntityAdapter::adapt($param, $offer);
                        $this->em->persist($offerParameter);
                    }
                }
                $newOffers[] = $offer;
                $results['new']++;
            }
        }

        $worker->setLastExecutedAt(new \DateTimeImmutable());
        $this->em->persist($worker);
        $this->em->flush();

        if(!empty($newOffers)){
            $this->createNotifications($newOffers, $worker);
        }

        return $results;
    }

    private function createNotifications(array $offers, Worker $worker): void
    {
        $workerIntegrations = $worker->getWorkerIntegrations();

        foreach ($workerIntegrations as $workerIntegration) {
            $integration = $workerIntegration->getIntegration();
            $notification = $this->integration->prepareNotification($offers, $worker, $integration);

            if($notification){
                $this->em->persist($notification);
            }
        }

        $this->em->flush();
    }

}