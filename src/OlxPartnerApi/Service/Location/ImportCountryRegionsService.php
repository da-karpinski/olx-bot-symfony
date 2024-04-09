<?php

namespace App\OlxPartnerApi\Service\Location;

use App\Entity\CountryRegion;
use App\OlxPartnerApi\Model\OlxPartnerApi;
use App\OlxPartnerApi\OlxPartnerApiInterface;
use App\OlxPartnerApi\Service\OAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImportCountryRegionsService
{
    private OlxPartnerApi $model;

    public function __construct(
        private readonly OlxPartnerApiInterface $apiClient,
        private readonly string $olxPartnerApiUrl,
        private readonly OAuthService $oauthService,
        private readonly EntityManagerInterface $em,
    )
    {
        $this->model = OlxPartnerApi::ListCountryRegions;
    }

    public function __invoke(): void
    {
        try {
            $response = $this->apiClient->request(
                $this->model->method(),
                $this->olxPartnerApiUrl . $this->model->uri(),
                ['headers' => $this->model->headers(($this->oauthService)())],
            );
        }catch (\Exception $e){
            if($e->getCode() === Response::HTTP_NOT_FOUND){
                throw new NotFoundHttpException('There are no country regions in the response.');
            }
        }

        foreach ($response[$this->model->dataKey()] as $region) {

            if($this->em->getRepository(CountryRegion::class)->findOneBy(['olxId' => $region['id']])){
                continue;
            }

            $countryRegion = new CountryRegion();
            $countryRegion->setOlxId($region['id']);
            $countryRegion->setName($region['name']);
            $this->em->persist($countryRegion);
        }

        $this->em->flush();
    }

}