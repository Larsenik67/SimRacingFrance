<?php

namespace App\Controller;

use App\Entity\Sujet;
use App\Entity\Messages;
use App\Form\EditSujetType;
use App\Form\SearchBarType;
use App\Form\CreateSujetType;
use App\Form\CreateResponseType;
use App\Repository\SujetRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            $teamSujet = $sujet->getTeam();

            if ( $teamSujet == null ){
            
                return $this->render('forum/forum_page.html.twig', [
                    'sujet' => $sujet,
                ]);

            } elseif ( $teamSujet != null ) {

                if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

                    $teamUser = $this->getUser()->getTeam();

                    if ($teamSujet == $teamUser){
                        
                        return $this->render('forum/forum_page.html.twig', [
                        'sujet' => $sujet,
                        ]);

                    } else {

                        return $this->redirectToRoute('app_forum');

                    }

                } else {
        
                    return $this->redirectToRoute('app_login');
        
                }
            }
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

    /**
     * @Route("/forum_create", name="app_forum_create")
     */
    public function sujetForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $sujet = new Sujet();
            $form = $this->createForm(CreateSujetType::class, $sujet);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $user = $this->getUser();
                $sujet->setUser($user);
                $sujet->setStatut(false);
                $sujet->setClosed(false);

                $entityManager->persist($sujet);
                $entityManager->flush();

                return $this->redirectToRoute('app_forum_id', ['id' => $sujet->getId()]);
            }

            return $this->render('forum/create_sujet.html.twig', [
                'form' => $form->createView(),
            ]);

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/forum_response/{id}", name="app_forum_response")
     */
    public function responseForm($id, ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $sujet = $doctrine
                        ->getRepository(Sujet::class)
                        ->findOneBy(array('id' => $id));

            $user = $this->getUser();
            $teamSujet = $sujet->getTeam();
            $teamUser = $user->getTeam();

            if ( $teamSujet == null || $teamSujet == $teamUser){

                $response = new Messages();
                $form = $this->createForm(CreateResponseType::class, $response);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $response->setUser($user);
                    $response->setSujet($sujet);
                    $response->setStatut(false);

                    $entityManager->persist($response);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_forum_id', ['id' => $id]);
                }

                return $this->render('forum/create_response.html.twig', [
                    'form' => $form->createView(),
                    'id' => $id,
                ]);

            } else {

                return $this->redirectToRoute('app_forum');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/forum_edit/{id}", name="app_forum_edit")
     */
    public function editSujetForm($id, ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $sujet = $doctrine
                        ->getRepository(Sujet::class)
                        ->findOneBy(array('id' => $id));

            $user = $this->getUser();
            $userSujet = $sujet->getUser();

            if ( $user == $userSujet ){

                $form = $this->createForm(EditSujetType::class);
                $form->get('titre')->setData($sujet->getTitre());
                $form->get('description')->setData($sujet->getDescription());
                $form->get('contenu')->setData($sujet->getContenu());
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $titre = $form->getData()->getTitre();
                    $description = $form->getData()->getDescription();
                    $contenu = $form->getData()->getContenu();

                    $sujet->setTitre($titre);
                    $sujet->setDescription($description);
                    $sujet->setContenu($contenu);

                    $entityManager->persist($sujet);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_forum_id', ['id' => $id]);
                }

                return $this->render('forum/forum_edit.html.twig', [
                    'form' => $form->createView(),
                    'sujet' => $sujet,
                ]);

            } else {

                return $this->redirectToRoute('app_forum');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }
}
