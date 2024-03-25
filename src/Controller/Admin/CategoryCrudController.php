<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class CategoryCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {}

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->disable(Action::EDIT);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('parent')
                ->setChoices($this->getMainCategories())
                ->setLabel('Main Category')
            );
    }

    private function getMainCategories()
    {
        $mainCategories = $this->em->getRepository(Category::class)->findBy(['parent' => null]);
        $choices = [];
        foreach ($mainCategories as $category) {
            $choices[$category->getName()] = $category->getId();
        }
        return $choices;
    }

}
