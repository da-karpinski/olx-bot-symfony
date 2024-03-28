<?php

namespace App\Controller;

use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/category')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CategoryService $categoryService,
    )
    {
    }

    #[Route(path: '/main', name: 'api_category_main_list')]
    public function listMainCategories(): Response
    {
        return $this->json($this->categoryService->getMainCategories());
    }

    #[Route(path: '/{id}/subcategories', name: 'api_category_subcategories_list')]
    public function listSubcategories(int $id): Response
    {
        return $this->json($this->categoryService->getSubcategories($id));
    }

    #[Route(path: '/{id}/attributes', name: 'api_category_attributes_list')]
    public function listAttributes(int $id): Response
    {
        return $this->json($this->categoryService->getCategoryAttributes($id));
    }
}
