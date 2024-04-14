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
            return $this->addPriceAttribute(
                ($this->getCategoryAttributesService)($category->getOlxId())
            );
        }else{
            throw new NotFoundHttpException($this->translator->trans('error.category.attributes-not-found', [], 'error'));
        }
    }

    private function addPriceAttribute(array $attributes): array
    {
        array_unshift($attributes, [
            'code' => 'price',
            'label' => 'Cena',
            'unit' => 'PLN',
            'validation' => [
                'numeric' => true,
                'min' => 0,
                'max' => 999999999
            ],
            'values' => []
        ]);

        return $attributes;
    }

}