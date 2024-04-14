<?php

namespace App\ApiResource\Category\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryGetSubcategoriesProvider implements ProviderInterface
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        $category = $this->em->getRepository(Category::class)->find($uriVariables['id']);

        if(!$category){
            throw new NotFoundHttpException($this->translator->trans('error.category.parent-not-found', [], 'error'));
        }

        $subcategories = $this->em->getRepository(Category::class)->findBy(['parent' => $category]);

        if(!$subcategories) {
            throw new NotFoundHttpException($this->translator->trans('error.subcategory.not-found', [], 'error'));
        }

        return $subcategories;
    }
}