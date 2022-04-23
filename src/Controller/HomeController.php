<?php

namespace App\Controller;

use App\Entity\HappyHours;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $happyHours = $entityManager
            ->getRepository(HappyHours::class)
            ->findAll();
        return $this->render('home/index.html.twig', [
            'happy_hours' => $happyHours,

        ]);
    }

    #[Route('/{id}', name: 'app_coming_soon', methods: ['GET'])]
    public function details(HappyHours $happyHour): Response
    {
        return $this->render('home/coming_soon.html.twig', [
            'happy_hour' => $happyHour,
        ]);
    }
}
