<?php

namespace App\OlxPublicApi\Adapter;

use App\Entity\Offer;
use App\Entity\Worker;

class OfferToEntityAdapter
{

    public static function adapt(array $offerData, Worker $worker): Offer
    {
        $offer = new Offer();
        $offer->setWorker($worker);
        $offer->setLastSeenAt(new \DateTimeImmutable());
        $offer->setCreatedAt(new \DateTimeImmutable($offerData['created_time']));
        $offer->setDescription($offerData['description']);

        if(isset($offerData['external_url'])) {
            $offer->setUrl($offerData['external_url']);
        }else{
            $offer->setUrl($offerData['url']);
        }

        $offer->setOlxId($offerData['id']);
        $offer->setRefreshedAt(new \DateTimeImmutable($offerData['last_refresh_time']));
        $offer->setTitle($offerData['title']);
        $offer->setValidTo(new \DateTimeImmutable($offerData['valid_to_time']));

        foreach ($offerData['params'] as $param) {
            if($param['key'] === 'price') {
                $offer->setPrice($param['value']['value']);
                $offer->setPriceCurrency($param['value']['currency']);
                break;
            }
        }

        return $offer;
    }
}