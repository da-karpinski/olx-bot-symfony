<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\CountryRegion;
use App\Entity\OlxPartnerLog;
use App\Entity\User;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CategoryService $categoryService,
    )
    {
    }

    #[Route('/', name: 'dashboard')]
    public function index(): Response
    {

        return $this->render('dashboard.html.twig', [
            'countryRegions' => $this->em->getRepository(CountryRegion::class)->findAll(),
            'mainCategories' => $this->categoryService->getMainCategories(),
        ]);


        //return $this->render('@EasyAdmin/page/content.html.twig');

        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('OLX Bot');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addJsFile("https://code.jquery.com/jquery-3.7.1.min.js")
            ->addCssFile("https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css")
            ->addJsFile("https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js")
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);

        yield MenuItem::section('Dictionaries');
        yield MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class);
        yield MenuItem::linkToCrud('Country regions', 'fa fa-map-location-dot', CountryRegion::class);
        yield MenuItem::linkToCrud('Cities', 'fa fa-city', City::class);

        yield MenuItem::section('Logs');
        yield MenuItem::linkToCrud('OLX Partner API logs', 'fa fa-bug', OlxPartnerLog::class);
        //yield MenuItem::linkToCrud('OLX Public API logs', 'fa fa-bug', OlxPublicLog::class); //TODO
    }
}
