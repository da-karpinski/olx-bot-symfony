<?php

namespace App\OlxPartnerApi\Adapter;

use App\Entity\City;
use App\Entity\CountryRegion;

class CityToEntityAdapter
{

    public static function adapt(array $olxCity, CountryRegion $countryRegion): City
    {
        $city = new City();
        $city->setOlxId($olxCity['id']);
        $city->setName($olxCity['name']);
        $city->setRegion($countryRegion);

        return $city;
    }

}