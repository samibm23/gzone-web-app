<?php

namespace App\Controller;

use App\Entity\Tournaments;
use App\Form\TournamentsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tournaments')]
class TournamentsController extends AbstractController
{
    #[Route('/', name: 'app_tournaments_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tournaments = $entityManager
            ->getRepository(Tournaments::class)
            ->findAll();

        return $this->render('tournaments/index.html.twig', [
            'tournaments' => $tournaments,
        ]);
    }

    #[Route('/new', name: 'app_tournaments_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tournament = new Tournaments();
        $form = $this->createForm(TournamentsType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tournament);
            $entityManager->flush();

            return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tournaments/new.html.twig', [
            'tournament' => $tournament,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournaments_show', methods: ['GET'])]
    public function show(Tournaments $tournament): Response
    {
        return $this->render('tournaments/show.html.twig', [
            'tournament' => $tournament,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tournaments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TournamentsType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tournaments/edit.html.twig', [
            'tournament' => $tournament,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournaments_delete', methods: ['POST'])]
    public function delete(Request $request, Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tournament->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tournament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
    }
}
