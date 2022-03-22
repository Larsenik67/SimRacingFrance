<?php

namespace App\Controller;

use App\Entity\Jeu;
use App\Entity\Team;
use App\Form\SearchBarType;
use App\Form\CreateTeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    /**
     * @Route("/team", name="app_team")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $teams = $doctrine
                ->getRepository(Team::class)
                ->findAll();     

        return $this->render('team/index.html.twig', [
            'teams' => $teams,
        ]);
    }

    /**
     * @Route("/team_search", name="app_team_search")
     */
    public function searchTeam(Request $request, TeamRepository $TeamRepo): Response
    {
        $form_search = $this->createForm(SearchBarType::class);
        $form_search->handleRequest($request);

        if($form_search->isSubmitted() && $form_search->isValid()){
            $search = $form_search->getData('search');
            $search = $search['search'];

            $teamSearch = $TeamRepo->searchTeamByName($search);
        }

        return $this->render('team/index.html.twig', [
            'search' => $search,
            'teams' => $teamSearch,
        ]);
    }

    public function searchBar(Request $request)
    {
        $form = $this->createForm(SearchBarType::class);
        $form->handleRequest($request);

        return $this->render('team/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/team_create", name="app_team_create")
     */
    public function teamForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $team = new Team();
        $form = $this->createForm(CreateTeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $team->addUser($user);
            $role = ["ROLE_MEMBRE"];
            $user->setRoleTeam($role);
            $entityManager->persist($team);
            $entityManager->flush();

            $this->addFlash('success', "La team a été crée avec succées !");
            return $this->redirectToRoute('app_team');
        }

        return $this->render('team/create_team.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
