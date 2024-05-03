<?php

namespace App\Service;

use App\Entity\Offer;
use App\Entity\OfferPhoto;
use App\OlxPublicApi\Service\GetOfferService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class OfferPhotoService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $olxPublicApiLogger,
        private readonly GetOfferService $getOfferService,
        #[Autowire('%offer_photo_upload_path%')]
        private readonly string $uploadPath
    )
    {
    }

    public function getPhotosForOffer(Offer $offer, ?array $publicApiResponse = null): array
    {

        $offerPhotos = [];

        if(is_null($publicApiResponse)){
            $publicApiResponse = ($this->getOfferService)($offer->getOlxId());
        }

        $photos = $publicApiResponse['photos'];

        foreach ($photos as $key => $photo){

            $offerPhoto = new OfferPhoto();
            $offerPhoto->setOlxId($photo['id'])
                ->setFileName($photo['filename'])
                ->setOffer($offer)
                ->setPhotoOrder($key);

            $photoUrl = str_replace(["{width}", "{height}"], [$photo['width'], $photo['height']], $photo['link']);
            $realFileName = $offer->getWorker()->getId() . '_' . $offer->getOlxId() . '_' . $key . '.webp';

            try{
                if(!file_put_contents($this->uploadPath . '/' . $realFileName, fopen($photoUrl, 'r'))){
                    $this->olxPublicApiLogger->error(sprintf("Error while saving photo %s", $photoUrl));
                    continue;
                }

                $offerPhoto->setRealFileName($realFileName);
                $this->em->persist($offerPhoto);
                $offerPhotos[] = $offerPhoto;

            }catch (\Exception $e){
                $this->olxPublicApiLogger->error(sprintf("Error while saving photo %s: %s", $photoUrl, $e->getMessage()));
                continue;
            }
        }

        return $offerPhotos;
    }

}