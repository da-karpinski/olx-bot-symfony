<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\CountryRegion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/city')]
class CityController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    #[Route(path: '/', name: 'api_city_list')]
    public function listCities(Request $request): Response
    {
        $queryParams = $request->query->all();

        if(isset($queryParams['countryRegion'])){
            if($countryRegion = $this->em->getRepository(CountryRegion::class)->find($queryParams['countryRegion'])){
                $cityCollection = $this->em->getRepository(City::class)->findBy(['region' => $countryRegion]);
            }
        }

        if(empty($cityCollection)){
            $cityCollection = $this->em->getRepository(City::class)->findAll();
        }

        $cities = [];
        foreach ($cityCollection as $city) {
            $cities[] = [
                'id' => $city->getId(),
                'name' => $city->getName(),
                'region' => [
                    "id" => $city->getRegion()->getId(),
                    "name" => $city->getRegion()->getName()
                ]
            ];
        }

        return $this->json($cities);
    }
}
