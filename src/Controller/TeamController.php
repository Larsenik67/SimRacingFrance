<?php

namespace App\Controller;

use App\Entity\Jeu;
use App\Entity\Team;
use App\Form\SearchBarType;
use App\Form\CreateTeamType;
use App\Repository\JeuRepository;
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
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

        $teams = $doctrine
                ->getRepository(Team::class)
                ->findAll();
        
        $equipe = $this->getUser()->getTeam();


        return $this->render('team/index.html.twig', [
            'teams' => $teams,
            'equipe' => $equipe,
        ]);
        }else{
            $teams = $doctrine
                ->getRepository(Team::class)
                ->findAll();

            return $this->render('team/index.html.twig', [
                'teams' => $teams,
            ]);
        }
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
            $role = ["ROLE_ADMIN"];
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

    /**
     * @Route("/team_leave", name="app_team_leave")
     */
    public function teamLeave(ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {

            $user = $this->getUser();
            $teamId = $user->getTeam()->getId();

            $team = $doctrine
                    ->getRepository(Team::class)
                    ->findOneBy(array('id' => $teamId));

            $team->removeUser($user);
            $role = [];
            $user->setRoleTeam($role);
            $entityManager->flush();

            $this->addFlash('success', "Vous avez quitté votre team");
            return $this->redirectToRoute('app_team');

        return $this->redirectToRoute('app_team');
    }

    /**
     * @Route("/team_join/{id}", name="app_team_join")
     */
    public function teamJoin($id, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
            $user = $this->getUser();

            $team = $doctrine
                    ->getRepository(Team::class)
                    ->findOneBy(array('id' => $id));

            $team->addUser($user);
            $role = ["ROLE_MEMBRE"];
            $user->setRoleTeam($role);
            $entityManager->flush();

            $this->addFlash('success', "Vous avez quitté votre team");
            return $this->redirectToRoute('app_team');

        return $this->redirectToRoute('app_team');
    }

    /**
     * @Route("/team_user", name="app_team_user")
     */
    public function teamUser(): Response
    {
            $team = $this->getUser()->getTeam();
            $users = $team->getUsers();

        return $this->render('team/team_user.html.twig', [
            'team' => $team,
            'users' => $users,
        ]);
    }

    /**
     * @Route("/team_sujet", name="app_team_sujet")
     */
    public function teamSujet(): Response
    {
            $team = $this->getUser()->getTeam();
            $sujets = $team->getSujets();

        return $this->render('team/team_sujet.html.twig', [
            'team' => $team,
            'sujets' => $sujets,
        ]);
    }

    /**
     * @Route("/team_event", name="app_team_event")
     */
    public function teamEvent(): Response
    {
            $team = $this->getUser()->getTeam();
            $events = $team->getEvenements();

        return $this->render('team/team_event.html.twig', [
            'team' => $team,
            'events' => $events,
        ]);
    }
}
