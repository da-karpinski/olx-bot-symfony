<?php

namespace App\OlxPublicApi\Service;

use App\OlxPublicApi\OlxPublicApiInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetOfferService
{

    public function __construct(
        private readonly string $olxPublicApiUrl,
        private readonly OlxPublicApiInterface $apiClient,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function __invoke(int $olxId): array
    {

        $response = $this->apiClient->request(
            'GET',
            $this->olxPublicApiUrl . '/' . $olxId,
            []
        );

        if(empty($response)){
            throw new GoneHttpException($this->translator->trans('error.olx-public-api.offer.gone', [], 'error'));
        }

        if(isset($response['error'])){
            if($response['error']['status'] === Response::HTTP_NOT_FOUND){
                throw new NotFoundHttpException($this->translator->trans('error.olx-public-api.offer.not-found', [], 'error'));
            }
        }

        return $response['data'];
    }

}