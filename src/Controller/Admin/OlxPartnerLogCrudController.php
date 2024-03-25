<?php

namespace App\Controller\Admin;

use App\Entity\OlxPartnerLog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class OlxPartnerLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OlxPartnerLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Olx Partner API logs')
            ->setDefaultSort(['created_at' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->disable(Action::EDIT);

    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('created_at')->setFormat('yyyy-MM-dd HH:mm:ss'),
            TextField::new('message')->renderAsHtml(),
            TextField::new('level'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('level')
                ->setChoices([
                    'DEBUG' => 'DEBUG',
                    'INFO' => 'INFO',
                    'NOTICE' => 'NOTICE',
                    'WARNING' => 'WARNING',
                    'ERROR' => 'ERROR',
                    'CRITICAL' => 'CRITICAL',
                    'ALERT' => 'ALERT',
                    'EMERGENCY' => 'EMERGENCY',
                ])
                ->setLabel('Level')
            );
    }

}
