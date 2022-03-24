<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchBarType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\SujetRepository;
use App\Repository\EvenementRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="app_search")
     */
    public function index(Request $request, UserRepository $UserRepo, TeamRepository $TeamRepo, EvenementRepository $EventRepo, SujetRepository $SujetRepo): Response
    {
        $form_search = $this->createForm(SearchBarType::class);
        $form_search->handleRequest($request);

        if($form_search->isSubmitted() && $form_search->isValid()){
            $search = $form_search->getData('search');
            $search = $search['search'];

            $users = $UserRepo->searchUserByName($search);
            $teams = $TeamRepo->searchTeamByName($search);
            $events = $EventRepo->searchEventByName($search);
            $sujets = $SujetRepo->searchSujetByTitle($search);

            return $this->render('search/index.html.twig', [
                'search' => $search,
                'users' => $users,
                'teams' => $teams,
                'events' => $events,
                'sujets' => $sujets,
            ]);
        } else {

            return $this->redirectToRoute('app_home');
            
        }
    }

    public function searchBar(Request $request)
    {
        $form = $this->createForm(SearchBarType::class);
        $form->handleRequest($request);

        return $this->render('search/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
