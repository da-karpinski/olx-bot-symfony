<?php

namespace App\OlxPartnerApi\Service\Category;

use App\Entity\Category;
use App\OlxPartnerApi\Adapter\CategoryToEntityAdapter;
use App\OlxPartnerApi\Model\OlxPartnerApi;
use App\OlxPartnerApi\OlxPartnerApiInterface;
use App\OlxPartnerApi\Service\OAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCategoryService
{
    private OlxPartnerApi $model;

    public function __construct(
        private readonly OlxPartnerApiInterface $apiClient,
        private readonly string $olxPartnerApiUrl,
        private readonly OAuthService $oauthService,
        private readonly EntityManagerInterface $em,
    )
    {
        $this->model = OlxPartnerApi::GetCategory;
    }

    public function __invoke(int $categoryId): Category
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
                throw new NotFoundHttpException('olx_partner_api.category.notFound');
            }
        }

        $olxCategory = $response[$this->model->dataKey()];

        if($olxCategory['parent_id'] === 0){
            $category = CategoryToEntityAdapter::adapt($olxCategory, null);
            $this->em->persist($category);

        }else{
            $parentCategory = $this->em->getRepository(Category::class)->findOneBy(['olxId' => $olxCategory['parent_id']]);
            if(!$parentCategory){
                try{
                    $parentCategory = ($this)($olxCategory['parent_id']);
                }catch (\Exception $e) {
                    throw new NotFoundHttpException('olx_partner_api.category.parent_not_found');
                }
            }
            $category = CategoryToEntityAdapter::adapt($olxCategory, $parentCategory);
            $this->em->persist($category);
        }

        $this->em->flush();
        return $category;
    }

}