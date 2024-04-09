<?php

namespace App\OlxPartnerApi\Service\Location;

use App\Entity\City;
use App\Entity\CountryRegion;
use App\OlxPartnerApi\Adapter\CityToEntityAdapter;
use App\OlxPartnerApi\Model\OlxPartnerApi;
use App\OlxPartnerApi\OlxPartnerApiInterface;
use App\OlxPartnerApi\Service\OAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImportCitiesService
{
    private OlxPartnerApi $model;

    public function __construct(
        private readonly OlxPartnerApiInterface $apiClient,
        private readonly string $olxPartnerApiUrl,
        private readonly OAuthService $oauthService,
        private readonly EntityManagerInterface $em,
    )
    {
        $this->model = OlxPartnerApi::ListCities;
    }

    public function __invoke(int $limit, int $offset): void
    {

        $response = $this->apiClient->request(
            $this->model->method(),
            $this->olxPartnerApiUrl . $this->model->uri(),
            [
                'headers' => $this->model->headers(($this->oauthService)()),
                'query' => [
                    'limit' => $limit,
                    'offset' => $offset
                ],
            ],
        );

        if (empty($response[$this->model->dataKey()])) {
            throw new NotFoundHttpException('There are no cities in the response.');
        }

        foreach ($response[$this->model->dataKey()] as $olxCity) {

            if($this->em->getRepository(City::class)->findOneBy(['olxId' => $olxCity['id']])){
                continue;
            }

            $countryRegion = $this->em->getRepository(CountryRegion::class)->findOneBy(['olxId' => $olxCity['region_id']]);
            $city = CityToEntityAdapter::adapt($olxCity, $countryRegion);
            $this->em->persist($city);
        }

        $this->em->flush();
        $this->em->clear();
    }

}