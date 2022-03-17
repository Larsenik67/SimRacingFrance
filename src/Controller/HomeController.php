<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Sujet;
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
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        
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
    }
}
