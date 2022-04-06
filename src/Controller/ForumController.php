<?php

namespace App\Controller;

use App\Entity\Sujet;
use App\Form\SearchBarType;
use App\Repository\SujetRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="app_forum")
     * @Route("/forum/{id}", name="app_forum_id")
     */
    public function index(ManagerRegistry $doctrine, int $id = null): Response
    {

        if( !$id ){

            $sujets = $doctrine
                    ->getRepository(Sujet::class)
                    ->findAll();

            return $this->render('forum/index.html.twig', [
                'sujets' => $sujets,
            ]);

        } elseif ( $id ){

            $sujet = $doctrine
                    ->getRepository(Sujet::class)
                    ->findOneBy(array('id' => $id));
            
            return $this->render('forum/forum_page.html.twig', [
                'sujet' => $sujet,
            ]);
        }
    }

    /**
     * @Route("/forum_search", name="app_forum_search")
     */
    public function searchTeam(Request $request, SujetRepository $SujetRepo): Response
    {
        $form_search = $this->createForm(SearchBarType::class);
        $form_search->handleRequest($request);

        if($form_search->isSubmitted() && $form_search->isValid()){
            $search = $form_search->getData('search');
            $search = $search['search'];

            $sujetSearch = $SujetRepo->searchSujetByTitle($search);
        }

        return $this->render('forum/index.html.twig', [
            'search' => $search,
            'sujets' => $sujetSearch,
        ]);
    }

    public function searchBar(Request $request)
    {
        $form = $this->createForm(SearchBarType::class);
        $form->handleRequest($request);

        return $this->render('forum/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
