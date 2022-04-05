<?php

namespace App\Controller;

use App\Entity\BadgeShips;
use App\Form\BadgeShipsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/badge/ships')]
class BadgeShipsController extends AbstractController
{
    #[Route('/', name: 'app_badge_ships_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $badgeShips = $entityManager
            ->getRepository(BadgeShips::class)
            ->findAll();

        return $this->render('badge_ships/index.html.twig', [
            'badge_ships' => $badgeShips,
        ]);
    }

    #[Route('/new', name: 'app_badge_ships_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $badgeShip = new BadgeShips();
        $form = $this->createForm(BadgeShipsType::class, $badgeShip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($badgeShip);
            $entityManager->flush();

            return $this->redirectToRoute('app_badge_ships_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('badge_ships/new.html.twig', [
            'badge_ship' => $badgeShip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_badge_ships_show', methods: ['GET'])]
    public function show(BadgeShips $badgeShip): Response
    {
        return $this->render('badge_ships/show.html.twig', [
            'badge_ship' => $badgeShip,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_badge_ships_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BadgeShips $badgeShip, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BadgeShipsType::class, $badgeShip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_badge_ships_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('badge_ships/edit.html.twig', [
            'badge_ship' => $badgeShip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_badge_ships_delete', methods: ['POST'])]
    public function delete(Request $request, BadgeShips $badgeShip, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$badgeShip->getId(), $request->request->get('_token'))) {
            $entityManager->remove($badgeShip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_badge_ships_index', [], Response::HTTP_SEE_OTHER);
    }
}
