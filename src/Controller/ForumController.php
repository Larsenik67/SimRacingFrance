<?php

namespace App\Controller;

use App\Entity\Sujet;
use App\Entity\Messages;
use App\Form\EditSujetType;
use App\Form\SearchBarType;
use App\Form\CreateSujetType;
use App\Form\DeleteSujetType;
use App\Form\EditMessageType;
use App\Form\DeleteMessageType;
use App\Form\CreateResponseType;
use App\Repository\SujetRepository;
use App\Repository\MessagesRepository;
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

    /**
     * @Route("/forum_delete/{id}", name="app_forum_delete")
     */
    public function sujetDelete($id, ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager, SujetRepository $sujetRepo, MessagesRepository $messageRepo): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){ //Vérifie de l'utilisateur est bien connecté

            $user = $this->getUser();   //Récupère l'utilisateur

            $sujet = $doctrine                              //Récupère le sujet a supprimer
                    ->getRepository(Sujet::class)
                    ->findOneBy(array('id' => $id));

            $sujetUser = $sujet->getUser();  //Récupère l'auteur du sujet
                
            if ($user == $sujetUser) {    //Vérifie que l'utilisateur est bien l'auteur du sujet

                $form = $this->createForm(DeleteSujetType::class);   //Crée le formulaire a partir du fichier src\Form\DeleteTeamType.php
                $form->handleRequest($request);     //Inspecte la requete lors de la soumission du formulaire, récupère les données et determine si le formulaire est valide

                if ($form->isSubmitted() && $form->isValid()) { //Vérifie que le formulaire est soumis et valide

                    $messages = $sujet->getMessages();   //Récupère les messages du sujet
                    foreach ($messages as $message){  //Pour chaque message :
                                                        
                        $messageRepo->remove($message);     //Crée la requete qui supprimera le message

                    }
                    
                    $sujetRepo->remove($sujet);   //Crée la requete qui supprimera le sujet
                    
                    $entityManager->flush();    //Envois la requete en base de donnée

                    $this->addFlash('success', "Le sujet a bien été supprimé");  //Ajoute un message qui sera afficher
                    return $this->redirectToRoute('app_forum');  //Redirection vers /forum
                }

                return $this->render('forum/forum_delete.html.twig', [
                    'sujet' => $sujet,
                    'form' => $form->createView(),
                    ]);

            } else {

                return $this->redirectToRoute('app_forum');
    
            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/forum_message_delete/{id}", name="app_forum_message_delete")
     */
    public function messageDelete($id, ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager, MessagesRepository $messageRepo): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){ //Vérifie de l'utilisateur est bien connecté

            $user = $this->getUser();   //Récupère l'utilisateur

            $message = $doctrine                              //Récupère le message a supprimer
                    ->getRepository(Messages::class)
                    ->findOneBy(array('id' => $id));
            
            $sujetId = $message->getSujet()->getId();

            $messageUser = $message->getUser();  //Récupère l'auteur du sujet
                
            if ($user == $messageUser) {    //Vérifie que l'utilisateur est bien l'auteur du sujet

                $form = $this->createForm(DeleteMessageType::class);   //Crée le formulaire a partir du fichier src\Form\DeleteTeamType.php
                $form->handleRequest($request);     //Inspecte la requete lors de la soumission du formulaire, récupère les données et determine si le formulaire est valide

                if ($form->isSubmitted() && $form->isValid()) { //Vérifie que le formulaire est soumis et valide
                                                        
                    $messageRepo->remove($message);     //Crée la requete qui supprimera le message
                    
                    $entityManager->flush();    //Envois la requete en base de donnée

                    $this->addFlash('success', "Le message a bien été supprimé");  //Ajoute un message qui sera afficher
                    return $this->redirectToRoute('app_forum_id', ['id' => $sujetId]);  //Redirection vers /forum
                }

                return $this->render('forum/forum_message_delete.html.twig', [
                    'message' => $message,
                    'form' => $form->createView(),
                    ]);

            } else {

                return $this->redirectToRoute('app_forum');
    
            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/forum_message_edit/{id}", name="app_forum_message_edit")
     */
    public function editMessageForm($id, ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $message = $doctrine
                        ->getRepository(Messages::class)
                        ->findOneBy(array('id' => $id));

            $user = $this->getUser();
            $userMessage = $message->getUser();

            if ( $user == $userMessage ){

                $form = $this->createForm(EditMessageType::class);
                $form->get('contenu')->setData($message->getContenu());
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $sujetId = $message->getSujet()->getId();

                    $contenu = $form->getData()->getContenu();

                    $message->setContenu($contenu);

                    $entityManager->persist($message);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_forum_id', ['id' => $sujetId]);
                }

                return $this->render('forum/forum_message_edit.html.twig', [
                    'form' => $form->createView(),
                    'message' => $message,
                ]);

            } else {

                return $this->redirectToRoute('app_forum');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }
}
