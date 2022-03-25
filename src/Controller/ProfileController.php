<?php

namespace App\Controller;

use App\Form\EditUserType;
use App\Form\DisableProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     * @Route("/profile/{id}", name="app_profile_id")
     */
    public function index(ManagerRegistry $doctrine, UserRepository $UserRepo, int $id = null): Response
    {
        if(!$id)
        {

            if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

                return $this->render('profile/index.html.twig', [
                ]);

            } else {

                return $this->redirectToRoute('app_login');

            }

        } elseif ( $id ) {

            $verified = true;
            $user = $UserRepo->searchConfirmedUserById($id, $verified);

            if ( $user )
            {

                return $this->render('profile/profile_page.html.twig', [
                    'user' => $user,
                    ]);

            } elseif ( !$user )
            {

                return $this->redirectToRoute('app_search');

            }

        }
    }

    /**
     * @Route("/profile_edit", name="app_profile_edit")
     */
    public function editUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $user = $this->getUser();

            $form = $this->createForm(EditUserType::class);
            $form->get('jeu')->setData($user->getJeu());
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                    $nom = $form->getData()->getNom();
                    $email = $form->getData()->getEmail();

                    if ($nom){
                    $user->setNom($nom);
                    }

                    if ($email){
                    $user->setEmail($email);
                    }

                    $entityManager->flush();

                    $this->addFlash('success', "Les informations ont bien été mise à jour !");
                    return $this->redirectToRoute('app_profile');

            }

            return $this->render('profile/profile_edit.html.twig', [
            'form' => $form->createView(),
            ]);
            
        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/profile_disable", name="app_profile_disable")
     */
    public function disableUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $user = $this->getUser();

            if ( $user->getRoleTeam() != ["ROLE_ADMIN", "ROLE_FONDATEUR"] )
            {

                $form = $this->createForm(DisableProfileType::class);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    
                    $team = $user->getTeam();
                    $role = [];

                    if ( $team ){

                        $team->removeUser($user);
                        $user->setIsVerifiedTeam(false);
                        $user->setRoleTeam($role);

                    }

                    $user->setStatut(true);

                    $entityManager->flush();

                    $this->addFlash('success', "Les informations ont bien été mise à jour !");
                    return $this->redirectToRoute('app_logout');

                }

                return $this->render('profile/profile_disable.html.twig', [
                'form' => $form->createView(),
                ]);

            } else {

                return $this->redirectToRoute('app_login');

            }
            
        } else {

            return $this->redirectToRoute('app_login');

        }
    }
}
