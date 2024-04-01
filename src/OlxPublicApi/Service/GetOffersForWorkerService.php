<?php

namespace App\OlxPublicApi\Service;

use App\Entity\CategoryAttribute;
use App\Entity\Worker;
use App\OlxPublicApi\OlxPublicApiInterface;
use Doctrine\Common\Collections\Collection;

class GetOffersForWorkerService
{

    public function __construct(
        private readonly string $olxPublicApiUrl,
        private readonly OlxPublicApiInterface $apiClient,
    )
    {
    }

    public function __invoke(Worker $worker): array
    {

        $defaultQueryParams = [
            'limit' => 50,
            'offset' => 0,
            'sort_by' => 'created_at:desc',
        ];

        $workerQueryParams = [
            'city_id' => $worker->getCity()->getOlxId(),
            'region_id' => $worker->getCity()->getRegion()->getOlxId(),
            'category_id' => $worker->getCategory()->getOlxId(),
        ];

        $response = $this->apiClient->request(
            'GET',
            $this->olxPublicApiUrl,
            [
                'query' => array_merge(
                    $defaultQueryParams,
                    $workerQueryParams,
                    $this->createQueryParamFromCategoryAttributes(
                        $worker->getCategoryAttributes()
                    )
                ),
            ],
        );

        return $response['data'];
    }

    private function createQueryParamFromCategoryAttributes(Collection $attributes): array
    {
        $query = [];
        /** @var CategoryAttribute $attribute */
        foreach ($attributes as $attribute) {

            if(str_ends_with($attribute->getAttributeCode(), ':to') or
                str_ends_with($attribute->getAttributeCode(), ':from')
            ){
                $key = 'filter_float_' . $attribute->getAttributeCode();
            }else{
                $key = 'filter_enum_' . $attribute->getAttributeCode();
            }
            $query[$key] = $attribute->getAttributeValue();
        }
        return $query;
    }

}