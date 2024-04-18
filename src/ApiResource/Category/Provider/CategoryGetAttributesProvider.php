<?php

namespace App\ApiResource\Category\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Category;
use App\OlxPartnerApi\Service\Category\GetCategoryAttributesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryGetAttributesProvider implements ProviderInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly GetCategoryAttributesService $getCategoryAttributesService
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        if($category = $this->em->getRepository(Category::class)->find($uriVariables['id'])){
            return ($this->getCategoryAttributesService)($category->getOlxId());

        }else{
            throw new NotFoundHttpException($this->translator->trans('error.category.attributes-not-found', [], 'error'));
        }
    }

}