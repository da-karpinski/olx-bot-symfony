<?php

namespace App\OlxPartnerApi\Service\Category;

use App\OlxPartnerApi\Model\OlxPartnerApi;
use App\OlxPartnerApi\OlxPartnerApiInterface;
use App\OlxPartnerApi\Service\OAuthService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCategoryAttributesService
{
    private OlxPartnerApi $model;

    public function __construct(
        private readonly OlxPartnerApiInterface $apiClient,
        private readonly string $olxPartnerApiUrl,
        private readonly OAuthService $oauthService,
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
                throw new NotFoundHttpException('olx_partner_api.category_attributes.notFound');
            }
        }

        return $response[$this->model->dataKey()];
    }

}