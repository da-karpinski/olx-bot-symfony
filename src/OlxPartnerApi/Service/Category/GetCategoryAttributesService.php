<?php

namespace App\OlxPartnerApi\Service\Category;

use App\OlxPartnerApi\Model\OlxPartnerApi;
use App\OlxPartnerApi\OlxPartnerApiInterface;
use App\OlxPartnerApi\Service\OAuthService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetCategoryAttributesService
{
    private OlxPartnerApi $model;

    public function __construct(
        private readonly OlxPartnerApiInterface $apiClient,
        private readonly string $olxPartnerApiUrl,
        private readonly OAuthService $oauthService,
        private readonly TranslatorInterface $translator
    )
    {
        $this->model = OlxPartnerApi::GetCategoryAttributes;
    }

    public function __invoke(int $categoryId): array
    {
        $uri = str_replace('{category_id}', $categoryId, $this->model->uri());

        try {
            $response = $this->apiClient->request(
                $this->model->method(),
                $this->olxPartnerApiUrl . $uri,
                ['headers' => $this->model->headers(($this->oauthService)())],
            );
        }catch (\Exception $e){
            if($e->getCode() === Response::HTTP_NOT_FOUND){
                throw new NotFoundHttpException($this->translator->trans('error.olx-partner-api.category.attributes-not-found'));
            }
        }

        array_unshift($response[$this->model->dataKey()], [
            'code' => 'price',
            'label' => 'Cena',
            'unit' => 'PLN',
            'validation' => [
                'numeric' => true,
                'min' => 0,
                'max' => 10000000
            ],
            'values' => []
        ]);

        return $response[$this->model->dataKey()];
    }

}