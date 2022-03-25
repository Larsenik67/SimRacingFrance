<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Entity\Sujet;
use App\Entity\Evenement;
use App\Form\SearchBarType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $user = $this->getUser();

            if ($user->getStatut() == true) {
                
                $user->setStatut(false);

                $entityManager->flush();
            }
            
            $events = $doctrine
                    ->getRepository(Evenement::class)
                    ->findAll();

            $sujets = $doctrine
                    ->getRepository(Sujet::class)
                    ->findAll();

            return $this->render('home/index.html.twig', [
                'events' => $events,
                'sujets' => $sujets,
            ]);

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/news", name="app_news")
     */
    public function news(): Response
    {
        return $this->render('home/news.html.twig');
    }
}
