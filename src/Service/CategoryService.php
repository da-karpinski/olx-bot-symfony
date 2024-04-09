<?php

namespace App\Service;

use App\Entity\Category;
use App\OlxPartnerApi\Service\Category\GetCategoryAttributesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GetCategoryAttributesService $getCategoryAttributesService,
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function getMainCategories(): array
    {
        $mainCategories = $this->em->getRepository(Category::class)->findBy(['parent' => null]);
        return $this->toArray($mainCategories);
    }

    public function getSubcategories(int $parentId): array
    {
        if($subcategories = $this->em->getRepository(Category::class)->findBy(['parent' => $parentId])){
            return $this->toArray($subcategories);
        }else{
            throw new NotFoundHttpException($this->translator->trans('error.category.not-found', [], 'error'));
        }
    }

    public function getCategoryAttributes(int $categoryId): array
    {
        if($category = $this->em->getRepository(Category::class)->find($categoryId)){
            return $this->addPriceAttribute(
                ($this->getCategoryAttributesService)($category->getOlxId())
            );
        }else{
            throw new NotFoundHttpException($this->translator->trans('error.category.attributes-not-found', [], 'error'));
        }

    }

    public function addPriceAttribute(array $attributes): array
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

    private function toArray(array $collection): array
    {
        $array = [];
        foreach ($collection as $item) {
            $array[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'parent' => $item->getParent()?->getId(),
                'hasChildren' => (bool)$this->em->getRepository(Category::class)->findBy(['parent' => $item->getId()]),
            ];
        }
        return $array;
    }

}