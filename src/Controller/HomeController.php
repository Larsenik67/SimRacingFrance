<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Sujet;
use App\Entity\Team;
use App\Entity\User;
use App\Form\SearchBarType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {

        $form = $this->createForm(SearchBarType::class);
        $form->handleRequest($request);
        
        $events = $doctrine
                ->getRepository(Evenement::class)
                ->findAll();

        $sujets = $doctrine
                ->getRepository(Sujet::class)
                ->findAll();

        return $this->render('home/index.html.twig', [
            'events' => $events,
            'sujets' => $sujets,
            'form' => $form->createView(),
        ]);
    }

    /**
   * @Route("/search", name="ajax_search")
   * @Method("GET")
   */
  /*public function searchAction(Request $request)
  {
    $teams = $doctrine
            ->getRepository(Team::class)
            ->findAll();

      $em = $this->getDoctrine()->getManager();

      $requestString = $request->get('q');

      $entities =  $em->getRepository('AppBundle:Entity')->findEntitiesByString($requestString);

      if(!$entities) {
          $result['entities']['error'] = "keine Einträge gefunden";
      } else {
          $result['entities'] = $this->getRealEntities($entities);
      }

      return new Response(json_encode($result));
  }

  public function getRealEntities($entities){

      foreach ($entities as $entity){
          $realEntities[$entity->getId()] = $entity->getFoo();
      }

      return $realEntities;
  }*/

    /**
     * @Route("/news", name="app_news")
     */
    public function news(): Response
    {
        return $this->render('home/news.html.twig');
    }
}
