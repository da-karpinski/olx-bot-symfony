<?php

namespace App\OlxPublicApi\Adapter;

use App\Entity\Offer;
use App\Entity\OfferParameter;

class OfferParameterToEntityAdapter
{

    public static function adapt(array $offerParameters, Offer $offer): OfferParameter
    {
        $offerParameter = new OfferParameter();
        $offerParameter->setOffer($offer);
        $offerParameter->setParameterKey($offerParameters['key']);
        $offerParameter->setParameterName($offerParameters['name']);
        $offerParameter->setValueKey($offerParameters['value']['key']);
        $offerParameter->setValueLabel($offerParameters['value']['label']);

        return $offerParameter;
    }
}