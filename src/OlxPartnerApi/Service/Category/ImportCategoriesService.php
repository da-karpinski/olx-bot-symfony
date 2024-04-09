<?php

namespace App\OlxPartnerApi\Service\Category;

use App\Entity\Category;
use App\OlxPartnerApi\Adapter\CategoryToEntityAdapter;
use App\OlxPartnerApi\Model\OlxPartnerApi;
use App\OlxPartnerApi\OlxPartnerApiInterface;
use App\OlxPartnerApi\Service\OAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImportCategoriesService
{
    private OlxPartnerApi $model;

    public function __construct(
        private readonly OlxPartnerApiInterface $apiClient,
        private readonly string $olxPartnerApiUrl,
        private readonly OAuthService $oauthService,
        private readonly EntityManagerInterface $em,
        private readonly GetCategoryService $getCategoryService,
    )
    {
        $this->model = OlxPartnerApi::ListCategories;
    }

    public function __invoke(): void
    {
        $response = $this->apiClient->request(
            $this->model->method(),
            $this->olxPartnerApiUrl . $this->model->uri(),
            ['headers' => $this->model->headers(($this->oauthService)())],
        );

        if (empty($response[$this->model->dataKey()])) {
            throw new NotFoundHttpException('There are no categories in the response.');
        }

        foreach ($response[$this->model->dataKey()] as $olxCategory) {

            if($this->em->getRepository(Category::class)->findOneBy(['olxId' => $olxCategory['id']])){
                continue;
            }

            if($olxCategory['parent_id'] === 0){
                $category = CategoryToEntityAdapter::adapt($olxCategory, null);
                $this->em->persist($category);

            }else{
                $parentCategory = $this->em->getRepository(Category::class)->findOneBy(['olxId' => $olxCategory['parent_id']]);
                if(!$parentCategory){
                    try{
                        $parentCategory = ($this->getCategoryService)($olxCategory['parent_id']);
                    }catch (\Exception $e) {
                        throw new NotFoundHttpException('Parent category not found.');
                    }
                }
                $category = CategoryToEntityAdapter::adapt($olxCategory, $parentCategory);
                $this->em->persist($category);
            }

            $this->em->flush();
        }
    }

}