<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Form\EditTeamType;
use App\Form\SearchBarType;
use App\Form\CreateTeamType;
use App\Form\DeleteTeamType;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
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
     * @Route("/team/{id}", name="app_team_id")
     */
    public function index(ManagerRegistry $doctrine, UserRepository $UserRepo, int $id = null): Response
    {

        if( !$id ){

            $teams = $doctrine
                    ->getRepository(Team::class)
                    ->findAll();

            if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

                $team = $this->getUser()->getTeam();

                if( !$team ){

                    return $this->render('team/index.html.twig', [
                        'teams' => $teams,
                    ]);

                } elseif ( $team ) {

                    $role = $this->getUser()->getRoleTeam();

                    if( $role[0] == "ROLE_ADMIN" || $role[0] == "ROLE_MODO" ){

                    $teamId = $team->getId();
                    $verified = false;
                    $users = $UserRepo->searchUserTeamConfirmation($teamId, $verified);
                    $count = count($users);

                    return $this->render('team/index.html.twig', [
                        'teams' => $teams,
                        'count' => $count,
                    ]);

                    } else {

                        return $this->render('team/index.html.twig', [
                            'teams' => $teams,
                        ]);

                    }
                }

            }else{

                return $this->render('team/index.html.twig', [
                    'teams' => $teams,
                ]);

            }

        } elseif ( $id ){

            $team = $doctrine
                    ->getRepository(Team::class)
                    ->findOneBy(array('id' => $id));

            $verified = true;
            $users = $UserRepo->searchUserTeamConfirmation($id, $verified);
            $count = count($users);
            
            return $this->render('team/team_page.html.twig', [
                'team' => $team,
                'users' => $users,
                'count' => $count
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
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            if($this->getUser()->getTeam() == null){

                $team = new Team();
                $form = $this->createForm(CreateTeamType::class, $team);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $user = $this->getUser();
                    $team->addUser($user);
                    $role = ["ROLE_ADMIN", "ROLE_FONDATEUR"];
                    $user->setRoleTeam($role);
                    $entityManager->persist($team);
                    $entityManager->flush();

                    $this->addFlash('success', "La team a été crée avec succées !");
                    return $this->redirectToRoute('app_team');
                }

                return $this->render('team/create_team.html.twig', [
                    'form' => $form->createView(),
                ]);
            } else {
                return $this->redirectToRoute('app_team');
            }
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/team_edit", name="app_team_edit")
     */
    public function teamEdit(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $user = $this->getUser();
            $team = $user->getTeam();
            $role = $user->getRoleTeam();

            if ($team != null) {

                if ($role[0] == "ROLE_ADMIN") {

                    $form = $this->createForm(EditTeamType::class);
                    $form->get('jeu')->setData($team->getJeu());
                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {

                        $nom = $form->getData()->getNom();
                        $description = $form->getData()->getDescription();
    
                        if ($nom){
                        $team->setNom($nom);
                        }
    
                        if ($description){
                        $team->setDescription($description);
                        }
    
                        $entityManager->flush();
    
                        $this->addFlash('success', "Les informations ont bien été mise à jour !");
                        return $this->redirectToRoute('app_team');
                    }

                    return $this->render('team/team_edit.html.twig', [
                        'form' => $form->createView(),
                        ]);

                } else {
    
                    return $this->redirectToRoute('app_team');
        
                }

            } else {

                return $this->redirectToRoute('app_team');
    
            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/team_delete", name="app_team_delete")
     */
    public function teamDelete(Request $request, EntityManagerInterface $entityManager, TeamRepository $teamRepo): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $user = $this->getUser();
            $team = $user->getTeam();
            $role = $user->getRoleTeam();

            if ($team != null) {

                if ($role == ["ROLE_ADMIN", "ROLE_FONDATEUR"]) {

                    $form = $this->createForm(DeleteTeamType::class);
                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {

                        $members = $team->getUsers();
                        foreach ($members as $member){

                            $role = [];
                            $member->setRoleTeam($role);
                            $member->setIsVerifiedTeam(false);
                            $team->removeUser($member);

                        }
                        
                        $teamRepo->remove($team);
                        
                        $entityManager->flush();
    
                        $this->addFlash('success', "Les informations ont bien été mise à jour !");
                        return $this->redirectToRoute('app_team');
                    }

                    return $this->render('team/team_delete.html.twig', [
                        'form' => $form->createView(),
                        ]);

                } else {
    
                    return $this->redirectToRoute('app_team');
        
                }

            } else {

                return $this->redirectToRoute('app_team');
    
            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/team_leave", name="app_team_leave")
     * @Route("/team_leave/{id}", name="app_team_leave_id")
     */
    public function teamLeave(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, int $id = null): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            if( !$id ){

                if($this->getUser()->getTeam() !== null){

                    $user = $this->getUser();

                    if( $user->getRoleTeam() !== ["ROLE_ADMIN", "ROLE_FONDATEUR"]){

                        $team = $user->getTeam();

                        $team->removeUser($user);
                        $role = [];
                        $user->setRoleTeam($role);
                        $user->setIsVerifiedTeam(false);
                        $entityManager->flush();

                        $this->addFlash('success', "Vous avez quitté votre team");
                        return $this->redirectToRoute('app_team');

                    } else {

                        return $this->redirectToRoute('app_team');

                    }

                } else {

                    return $this->redirectToRoute('app_team');

                }

            } elseif ( $id ){

                $user = $doctrine
                        ->getRepository(User::class)
                        ->findOneBy(array('id' => $id));
                
                $admin = $this->getUser();
                $teamUser = $user->getTeam();
                $teamAdmin = $admin->getTeam();

                if($teamUser == $teamAdmin){

                    if( $user->getRoleTeam() !== ["ROLE_ADMIN", "ROLE_FONDATEUR"]){

                        if($admin->getRoleTeam()[0] == "ROLE_ADMIN" || $admin->getRoleTeam()[0] == "ROLE_MODO"){

                            if( $admin->getRoleTeam() == ["ROLE_ADMIN"] && $user->getRoleTeam() == ["ROLE_ADMIN"] )
                            {

                                return $this->redirectToRoute('app_team_user');

                            } elseif ( $admin->getRoleTeam() == ["ROLE_MODO"] && $user->getRoleTeam() == ["ROLE_ADMIN"] )
                            {

                                return $this->redirectToRoute('app_team_user');

                            }elseif ( $admin->getRoleTeam() == ["ROLE_MODO"] && $user->getRoleTeam() == ["ROLE_MODO"] )
                            {

                                return $this->redirectToRoute('app_team_user');

                            } else {
                                    
                                $teamUser->removeUser($user);
                                $role = [];
                                $user->setRoleTeam($role);
                                $user->setIsVerifiedTeam(false);
                                $entityManager->flush();

                                return $this->redirectToRoute('app_team_user');

                            }

                        } else {

                            return $this->redirectToRoute('app_team');
            
                        }

                    } else {

                        return $this->redirectToRoute('app_team');
        
                    }
                } else {

                    return $this->redirectToRoute('app_team');
        
                }
            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/team_join/{id}", name="app_team_join")
     */
    public function teamJoin($id, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $user = $this->getUser();

            if($user->getTeam() == null){

                $team = $doctrine
                        ->getRepository(Team::class)
                        ->findOneBy(array('id' => $id));

                $team->addUser($user);
                $role = ["ROLE_MEMBRE"];
                $user->setRoleTeam($role);
                $entityManager->flush();

                $this->addFlash('success', "Vous avez quitté votre team");
                return $this->redirectToRoute('app_team');

            }else {

                return $this->redirectToRoute('app_team');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/team_user", name="app_team_user")
     */
    public function teamUser(UserRepository $UserRepo): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $team = $this->getUser()->getTeam();

            if( $team ) 
            {

                $teamId = $team->getId();
                $verified = true;
                $users = $UserRepo->searchUserTeamConfirmation($teamId, $verified);

                return $this->render('team/team_user.html.twig', [
                    'users' => $users,
                ]);

            } elseif ( !$team )
            {

                return $this->redirectToRoute('app_team');
                
            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/team_sujet", name="app_team_sujet")
     */
    public function teamSujet(): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $team = $this->getUser()->getTeam();

            if( $team ) 
            {

                return $this->render('team/team_sujet.html.twig');

            } elseif ( !$team )
            {

                return $this->redirectToRoute('app_team');
                
            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/team_event", name="app_team_event")
     */
    public function teamEvent(): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $team = $this->getUser()->getTeam();

            if( $team ) 
            {

                return $this->render('team/team_event.html.twig');

            } elseif ( !$team )
            {

                return $this->redirectToRoute('app_team');
                
            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/team_confirmation", name="app_team_confirmation")
     * @Route("/team_confirmation/{id}", name="app_team_confirmation_id")
     */
    public function teamConfirmation(EntityManagerInterface $entityManager, ManagerRegistry $doctrine, UserRepository $UserRepo, int $id = null): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $admin = $this->getUser();

            if($admin->getRoleTeam()[0] == "ROLE_ADMIN" || $admin->getRoleTeam()[0] == "ROLE_MODO")
            {
            
                $teamAdmin = $admin->getTeam();

                if ( !$id )
                {
        
                    $teamId = $teamAdmin->getId();
                    $verified = false;
                    $users = $UserRepo->searchUserTeamConfirmation($teamId, $verified);

                return $this->render('team/team_confirm_user.html.twig', [
                    'users' => $users,
                ]);

                } elseif ( $id )
                {

                    $user = $doctrine
                            ->getRepository(User::class)
                            ->findOneBy(array('id' => $id));

                    $teamUser = $user->getTeam();
                    
                    if ($teamAdmin == $teamUser){

                        $verified = 1;
                        $user->setIsVerifiedTeam($verified);
                        $entityManager->flush();

                        return $this->redirectToRoute('app_team_confirmation');

                    } else {

                        return $this->redirectToRoute('app_team_confirmation');

                    }
                }

            } else {

                return $this->redirectToRoute('app_team');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/promote_user/{id}", name="app_promote_user")
     */
    public function promoteUser($id, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $admin = $this->getUser();

            if($admin->getRoleTeam()[0] == "ROLE_ADMIN"){

                $user = $doctrine
                        ->getRepository(User::class)
                        ->findOneBy(array('id' => $id));
                
                $team = $user->getTeam();
                $teamAdmin = $admin->getTeam();

                if($team == $teamAdmin){

                    $role = $user->getRoleTeam();

                    if ($role == ["ROLE_MODO"]){

                        if($admin->getRoleTeam() == ["ROLE_ADMIN", "ROLE_FONDATEUR"]){

                        $newRole = ["ROLE_ADMIN"];

                        } else {

                            return $this->redirectToRoute('app_team_user');

                        }

                    } elseif ($role == ["ROLE_MEMBRE"]){

                        $newRole = ["ROLE_MODO"];

                    } else {

                        return $this->redirectToRoute('app_team_user');
    
                    }

                    $user->setRoleTeam($newRole);
                    $entityManager->flush();

                    $this->addFlash('success', "Vous avez quitté votre team");
                    return $this->redirectToRoute('app_team_user');

                } else {

                    return $this->redirectToRoute('app_team_user');

                }

            } else {

                return $this->redirectToRoute('app_team');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }

    }

    /**
     * @Route("/demote_user/{id}", name="app_demote_user")
     */
    public function demoteUser($id, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $admin = $this->getUser();

            if($admin->getRoleTeam()[0] == "ROLE_ADMIN"){

                $user = $doctrine
                        ->getRepository(User::class)
                        ->findOneBy(array('id' => $id));

                $teamUser = $user->getTeam();
                $teamAdmin = $admin->getTeam();
                
                

                if($teamUser == $teamAdmin){

                    $role = $user->getRoleTeam();

                    if($role == ["ROLE_ADMIN"])
                    {
                        
                        if($admin->getRoleTeam() == ["ROLE_ADMIN", "ROLE_FONDATEUR"]){
                            
                            $newRole = ["ROLE_MODO"];

                        } else {

                            return $this->redirectToRoute('app_team_user');
    
                        }

                    } elseif ($role == ["ROLE_MODO"])
                    {

                        $newRole = ["ROLE_MEMBRE"];

                    } else {

                        return $this->redirectToRoute('app_team_user');
    
                    }

                    $user->setRoleTeam($newRole);
                    $entityManager->flush();

                    $this->addFlash('success', "Vous avez quitté votre team");
                    return $this->redirectToRoute('app_team_user');

                } else {

                    return $this->redirectToRoute('app_team_user');

                }

            } else {

                return $this->redirectToRoute('app_team');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }

    }

    /**
     * @Route("/transfer_propriety/{id}", name="app_transfer_propriety")
     */
    public function transferPropriety($id, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $admin = $this->getUser();

            if ( $admin->getRoleTeam() == ["ROLE_ADMIN", "ROLE_FONDATEUR"] )
            {

                $user = $doctrine
                        ->getRepository(User::class)
                        ->findOneBy(array('id' => $id));

                $teamUser = $user->getTeam();
                $teamAdmin = $admin->getTeam();

                if($teamUser == $teamAdmin){

                    $role = ["ROLE_ADMIN"];
                    $newRole = ["ROLE_ADMIN", "ROLE_FONDATEUR"];

                    $admin->setRoleTeam($role);
                    $user->setRoleTeam($newRole);

                    $user->setRoleTeam($newRole);
                    $entityManager->flush();
            
                    $this->addFlash('success', "Vous avez transféré la propriété de votre team");
                    return $this->redirectToRoute('app_team_user');

                } else {

                    return $this->redirectToRoute('app_team_user');

                }

            } else {

                return $this->redirectToRoute('app_team_user');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }

    }
}
