<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Evenement;
use App\Form\EditEventType;
use App\Form\SearchBarType;
use App\Form\CreateEventType;
use App\Form\DeleteEventType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="app_event")
     * @Route("/event/{id}", name="app_event_id")
     */
    public function index(ManagerRegistry $doctrine, int $id = null): Response
    {

        if( !$id ){

            $events = $doctrine
                    ->getRepository(Evenement::class)
                    ->findAll();

                return $this->render('event/index.html.twig', [
                    'events' => $events,
                ]);

        } elseif ( $id ){

            $event = $doctrine
                    ->getRepository(Evenement::class)
                    ->findOneBy(array('id' => $id));
            
            return $this->render('event/event_page.html.twig', [
                'event' => $event,
            ]);
        }

    }

    public function searchBar(Request $request)
    {
        $form = $this->createForm(SearchBarType::class);
        $form->handleRequest($request);

        return $this->render('event/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event_search", name="app_event_search")
     */
    public function searchEvent(Request $request, EvenementRepository $EventRepo): Response
    {
        $form_search = $this->createForm(SearchBarType::class);
        $form_search->handleRequest($request);

        if($form_search->isSubmitted() && $form_search->isValid()){
            $search = $form_search->getData('search');
            $search = $search['search'];

            $eventSearch = $EventRepo->searchEventByName($search);
        }

        return $this->render('event/index.html.twig', [
            'search' => $search,
            'events' => $eventSearch,
        ]);
    }

    /**
     * @Route("/event_create", name="app_event_create")
     */
    public function eventForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $user = $this->getUser();
            $role = $user->getRoleTeam();

            if($this->getUser()->getTeam() != null && $role[0] == "ROLE_ADMIN"){

                $event = new Evenement();
                $form = $this->createForm(CreateEventType::class, $event);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $team = $user->getTeam();
                    $event->setTeam($team);
                    $entityManager->persist($event);
                    $entityManager->flush();


                    $this->addFlash('success', "L'évènement a été crée avec succées !");
                    return $this->redirectToRoute('app_event_id', ['id' => $event->getId()]);
                }

                return $this->render('event/create_event.html.twig', [
                    'form' => $form->createView(),
                ]);
            } else {
                return $this->redirectToRoute('app_event');
            }
        } else {
            return $this->redirectToRoute('app_login');
        }
    }


    /**
     * @Route("/event_edit/{id}", name="app_event_edit")
     */
    public function eventEdit(Request $request, ManagerRegistry $doctrine, EntityManagerInterface $entityManager, int $id = null): Response
    {

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            
            if( $id ){

                $user = $this->getUser();
                $team = $user->getTeam();
                $role = $user->getRoleTeam();

                $event = $doctrine
                        ->getRepository(Evenement::class)
                        ->findOneBy(array('id' => $id));

                        

                if ($team != null) {


                    if ($team == $event->getTeam()){

                        if ($role[0] == "ROLE_ADMIN") {

                            $form = $this->createForm(EditEventType::class);
                            $form->get('jeu')->setData($event->getJeu());
                            $form->get('dateTime')->setData($event->getDateTime());
                            $form->handleRequest($request);

                            if ($form->isSubmitted() && $form->isValid()) {

                                $nom = $form->getData()->getNom();
                                $description = $form->getData()->getDescription();
                                $nbPlace = $form->getData()->getNbPlace();
                                $dateTime = $form->getData()->getDateTime();
                                $jeu = $form->getData()->getJeu();
            
                                if ($nom){
                                    $event->setNom($nom);
                                }
            
                                if ($description){
                                    $event->setDescription($description);
                                }

                                if ($nbPlace){
                                    $event->setNbPlace($nbPlace);
                                }

                                if ($dateTime){
                                    $event->setDateTime($dateTime);
                                }

                                if ($jeu){
                                    $event->setJeu($jeu);
                                }
            
                                $entityManager->persist($event);
                                $entityManager->flush();
            
                                $this->addFlash('success', "Les informations ont bien été mise à jour !");
                                return $this->redirectToRoute('app_event_id', ['id' => $id]);
                            }

                            return $this->render('event/event_edit.html.twig', [
                                'event' => $event,
                                'form' => $form->createView(),
                                ]);

                        } else {
            
                            return $this->redirectToRoute('app_event');
                
                        }

                    } else {

                        return $this->redirectToRoute('app_event');

                    }

                } else {

                    return $this->redirectToRoute('app_event');
        
                }

            } else {

                return $this->redirectToRoute('app_event');

            }

        } else {

            return $this->redirectToRoute('app_login');
            
        }
    }

    /**
     * @Route("/event_join/{id}", name="app_event_join")
     */
    public function eventJoin($id, ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $user = $this->getUser();

            $event = $doctrine
                    ->getRepository(Evenement::class)
                    ->findOneBy(array('id' => $id));
            
            $contenders = $event->getUsers();

            if ( count($contenders) != $event->getNbPlace()){

                foreach ($contenders as $contender){

                    if ($contender == $user){

                        return $this->redirectToRoute('app_event_id', ['id' => $id]);

                    }
                }

                $event->addUser($user);
                $entityManager->flush();

                $this->addFlash('success', "Vous avez rejoins l'évènement");
                return $this->redirectToRoute('app_event_id', ['id' => $id]);

            } else {
                
                return $this->redirectToRoute('app_event_id', ['id' => $id]);

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/event_delete/{id}", name="app_event_delete")
     */
    public function eventDelete($id, ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager, EvenementRepository $eventRepo): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){ //Vérifie de l'utilisateur est bien connecté

            $user = $this->getUser();   //Récupère l'utilisateur
            $teamId = $user->getTeam()->getId();   //Récupère l'id de la team de l'utilisateur
            $role = $user->getRoleTeam();   //Récupère le role au sein de la team de l'utilisateur

            $event = $doctrine                              //Récupère l'évènement a supprimer
                    ->getRepository(Evenement::class)
                    ->findOneBy(array('id' => $id));

            $eventTeamId = $event->getTeam()->getId();  //Récupère l'id de la team aillant crée l'évènement

            if ($teamId != null) {    //Vérifie que l'utilisateur fasse bien partie d'une team
                
                if ($teamId == $eventTeamId && $role[0] == "ROLE_ADMIN") {    //Vérifie que l'évènement appartient bien a la team de l'utilisateur et que l'utilisateur est un administrateur de la team

                    $form = $this->createForm(DeleteEventType::class);   //Crée le formulaire a partir du fichier src\Form\DeleteTeamType.php
                    $form->handleRequest($request);     //Inspecte la requete lors de la soumission du formulaire, récupère les données et determine si le formulaire est valide

                    if ($form->isSubmitted() && $form->isValid()) { //Vérifie que le formulaire est soumis et valide

                        $members = $event->getUsers();   //Récupère les paticipants définie plus haut
                        foreach ($members as $member){  //Pour chaque participant a l'évènement :
                                                          
                            $event->removeUser($member);     //Crée la requete qui supprimera l'utilisateur de l'évènement

                        }
                        
                        $eventRepo->remove($event);   //Crée la requete qui supprimera l'évènement
                        
                        $entityManager->flush();    //Envois la requete en base de donnée
    
                        $this->addFlash('success', "L'évènement a bien été supprimé");  //Ajoute un message qui sera afficher
                        return $this->redirectToRoute('app_event');  //Redirection vers /event
                    }

                    return $this->render('event/event_delete.html.twig', [
                        'event' => $event,
                        'form' => $form->createView(),
                        ]);

                } else {
    
                    return $this->redirectToRoute('app_event');
        
                }

            } else {

                return $this->redirectToRoute('app_event');
    
            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/event_leave/{id}-{idUser}", name="app_event_leave")
     */
    public function eventLeave(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, int $idUser = null, int $id = null): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){


            if( $id && $idUser ){

                $event = $doctrine
                        ->getRepository(Evenement::class)
                        ->findOneBy(array('id' => $id));
                
                $admin = $this->getUser();
                $role = $admin->getRoleTeam();
                $teamEvent = $event->getTeam()->getId();
                $teamAdmin = $admin->getTeam()->getId();

                
                if($teamEvent == $teamAdmin && $role[0] == "ROLE_ADMIN"){

                    $user = $doctrine
                            ->getRepository(User::class)
                            ->findOneBy(array('id' => $idUser));
                                    
                    $event->removeUser($user);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_event_id', ['id' => $id]);

                } else {

                    return $this->redirectToRoute('app_event');
        
                }

            } else {

                return $this->redirectToRoute('app_event');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }
}
