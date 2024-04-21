<?php

namespace App\Service;

use App\Entity\Offer;
use App\Entity\Worker;
use App\Integration\IntegrationFactory;
use App\OlxPublicApi\Adapter\OfferParameterToEntityAdapter;
use App\OlxPublicApi\Adapter\OfferToEntityAdapter;
use App\OlxPublicApi\Service\GetOffersForWorkerService;
use Doctrine\ORM\EntityManagerInterface;

class OfferService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GetOffersForWorkerService $getOffersForWorkerService,
        private readonly IntegrationFactory $integrationFactory
    )
    {
    }

    public function processOffersForWorker(Worker $worker, bool $prefetch = false): array
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

            $offer->setPrefetched($prefetch);

            if($existingOffer = $this->em->getRepository(Offer::class)->findOneBy(['olxId' => $offer->getOlxId(), 'worker' => $worker])){
                $existingOffer->setLastSeenAt(new \DateTimeImmutable());
                $this->em->persist($existingOffer);
                $results['updated']++;

                /** Add new offer */
            }else{
                $this->em->persist($offer);

                if($prefetch) continue;

                foreach ($olxOffer['params'] as $param) {
                    if($param['key'] !== 'price') {
                        $offerParameter = OfferParameterToEntityAdapter::adapt($param, $offer);
                        $this->em->persist($offerParameter);
                    }
                }
                //TODO: download offer photos
                $newOffers[] = $offer;
                $results['new']++;
            }
        }

        $worker->setLastExecutedAt(new \DateTimeImmutable());

        if(!$prefetch){
            $this->em->persist($worker);
            $this->em->flush();

            if(!empty($newOffers)){
                $this->createNotifications($newOffers, $worker);
            }
        }

        return $results;
    }

    private function createNotifications(array $offers, Worker $worker): void
    {
        $workerIntegrations = $worker->getWorkerIntegrations();

        foreach ($workerIntegrations as $workerIntegration) {
            $integration = $workerIntegration->getIntegration();
            $notifications = $this->integrationFactory
                ->getIntegration($integration->getIntegrationType()->getIntegrationCode())
                ->prepareNotifications($offers, $worker, $integration);

            if(is_array($notifications)) {
                foreach ($notifications as $notification) {
                    $this->em->persist($notification);
                }
            }else{
                $this->em->persist($notifications);
            }
        }

        $this->em->flush();
    }

}