<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Games;

#[Route('/games/front')]
class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $games = $entityManager
            ->getRepository(Games::class)
            ->findAll();
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
