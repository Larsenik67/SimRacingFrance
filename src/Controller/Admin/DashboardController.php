<?php

namespace App\Controller\Admin;

use App\Entity\Jeu;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Sujet;
use App\Entity\Messages;
use App\Entity\Evenement;
use App\Controller\Admin\UserCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;


class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);
        return $this->redirect($routeBuilder->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SimRacing France');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-file-pdf', User::class);
        yield MenuItem::linkToCrud('Teams', 'fa fa-file-pdf', Team::class);
        yield MenuItem::linkToCrud('Evenements', 'fa fa-file-pdf', Evenement::class);
        yield MenuItem::linkToCrud('Sujets', 'fa fa-file-pdf', Sujet::class);
        yield MenuItem::linkToCrud('Réponses aux sujets', 'fa fa-file-pdf', Messages::class);
        yield MenuItem::linkToCrud('Jeux', 'fa fa-file-pdf', Jeu::class);
        yield MenuItem::linkToRoute('Retourner sur le site', 'fa fa-file-pdf', 'app_home');
    }
}
