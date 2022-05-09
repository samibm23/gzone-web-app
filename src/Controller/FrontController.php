<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Games;

#[Route('/games/front')]
class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {

          $repository=$this->getDoctrine()->getRepository(Games::class);
            $games= $repository->findAll();
            $games = $paginator->paginate(
            $games,
            $request->query->getInt('page', 1), 2
        );
        return $this->render('front/index.html.twig', [
            'games' => $games,
                ]);
    }
    /**
     * @Route("/tri", name="triname")
     */
    public function Tri(Request $request,PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();


        $query = $em->createQuery(
            'SELECT a FROM App\Entity\Games a 
            ORDER BY a.name ASC'
        );

        $games = $query->getResult();

        $games = $paginator->paginate(
            $games,
            $request->query->getInt('page',1),
            2
        );

        return $this->render('front/index.html.twig', [
            'games' => $games,
        ]);

    }

    #[Route('/{id}', name: 'app_front_details', methods: ['GET'])]

    public function details(Games $game) : Response{
        return $this->render('front/details.html.twig', [
            'game'=>$game,
        ]);

}

}
