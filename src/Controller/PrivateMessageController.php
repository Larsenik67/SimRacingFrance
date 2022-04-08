<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\MessagePrive;
use App\Entity\ReponsePrive;
use App\Form\CreatePrivateMessageType;
use App\Form\CreatePrivateResponseType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\MessagePriveRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrivateMessageController extends AbstractController
{
    /**
     * @Route("/private/message", name="app_private_message")
     */
    public function index(): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            return $this->render('private_message/index.html.twig', []);

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/private/inbox", name="app_private_message_inbox")
     */
    public function inbox(MessagePriveRepository $mpRepo): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $id = $this->getUser()->getId();
            $mp = $mpRepo->inbox($id);
            return $this->render('private_message/inbox.html.twig', [
                'mp' => $mp,
            ]);

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/private/inbox/{id}", name="app_private_message_inbox_page")
     */
    public function inboxMessage($id, ManagerRegistry $doctrine): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $mp = $doctrine
                        ->getRepository(MessagePrive::class)
                        ->findOneBy(array('id' => $id));

            $user = $this->getUser();
            $destinataire = $mp->getDestinataire();

            if ( $user == $destinataire ){

                return $this->render('private_message/inbox_page.html.twig', [
                    'mp' => $mp,
                ]);

            } else {

                return $this->redirectToRoute('app_private_message_inbox');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/private/response/{id}", name="app_private_message_response")
     */
    public function responseForm($id, ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $mp = $doctrine
                        ->getRepository(MessagePrive::class)
                        ->findOneBy(array('id' => $id));

            $user = $this->getUser();
            $destinataire = $mp->getDestinataire();
            $expediteur = $mp->getExpediteur();

            if ( $user == $destinataire || $user == $expediteur ){

                $response = new ReponsePrive();
                $form = $this->createForm(CreatePrivateResponseType::class, $response);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $response->setUser($user);
                    $response->setMessagePrive($mp);

                    $entityManager->persist($response);
                    $entityManager->flush();

                    if ( $user == $destinataire) {

                        return $this->redirectToRoute('app_private_message_inbox_page', ['id' => $id]);

                    } elseif ( $user == $expediteur ){

                        return $this->redirectToRoute('app_private_message_outbox_page', ['id' => $id]);

                    }
                }

                return $this->render('private_message/create_response.html.twig', [
                    'form' => $form->createView(),
                    'id' => $id,
                ]);

            } else {

                return $this->redirectToRoute('app_private_message_inbox');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/private/outbox", name="app_private_message_outbox")
     */
    public function outbox(MessagePriveRepository $mpRepo): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $id = $this->getUser()->getId();
            $mp = $mpRepo->outbox($id);
            return $this->render('private_message/outbox.html.twig', [
                'mp' => $mp,
            ]);

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/private/outbox/{id}", name="app_private_message_outbox_page")
     */
    public function outboxMessage($id, ManagerRegistry $doctrine): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $mp = $doctrine
                        ->getRepository(MessagePrive::class)
                        ->findOneBy(array('id' => $id));

            $user = $this->getUser();
            $expediteur = $mp->getExpediteur();

            if ( $user == $expediteur ){

                return $this->render('private_message/outbox_page.html.twig', [
                    'mp' => $mp,
                ]);

            } else {

                return $this->redirectToRoute('app_private_message_outbox');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }

    /**
     * @Route("/private/message/{id}", name="app_private_message_create")
     */
    public function messageForm($id, ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')){

            $destinataire = $doctrine
                        ->getRepository(User::class)
                        ->findOneBy(array('id' => $id));

            $expediteur = $this->getUser();

            if ( $destinataire->getStatut() == 0 ){

                $response = new MessagePrive();
                $form = $this->createForm(CreatePrivateMessageType::class, $response);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $response->setExpediteur($expediteur);
                    $response->setDestinataire($destinataire);

                    $entityManager->persist($response);
                    $entityManager->flush();

                    return $this->redirectToRoute('app_private_message_outbox_page', ['id' => $response->getId()]);

                }

                return $this->render('private_message/create_message.html.twig', [
                    'form' => $form->createView(),
                    'id' => $id,
                ]);

            } else {

                return $this->redirectToRoute('app_private_message_inbox');

            }

        } else {

            return $this->redirectToRoute('app_login');

        }
    }
}
