<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
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
        $subcategories = $this->em->getRepository(Category::class)->findBy(['parent' => $parentId]);
        return $this->toArray($subcategories);
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