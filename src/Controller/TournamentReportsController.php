<?php

namespace App\Controller;

use App\Entity\TournamentReports;
use App\Form\TournamentReportsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tournament/reports')]
class TournamentReportsController extends AbstractController
{
    #[Route('/', name: 'app_tournament_reports_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tournamentReports = $entityManager
            ->getRepository(TournamentReports::class)
            ->findAll();

        return $this->render('tournament_reports/index.html.twig', [
            'tournament_reports' => $tournamentReports,
        ]);
    }

    #[Route('/new', name: 'app_tournament_reports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tournamentReport = new TournamentReports();
        $form = $this->createForm(TournamentReportsType::class, $tournamentReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tournamentReport);
            $entityManager->flush();

            return $this->redirectToRoute('app_tournament_reports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tournament_reports/new.html.twig', [
            'tournament_report' => $tournamentReport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournament_reports_show', methods: ['GET'])]
    public function show(TournamentReports $tournamentReport): Response
    {
        return $this->render('tournament_reports/show.html.twig', [
            'tournament_report' => $tournamentReport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tournament_reports_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TournamentReports $tournamentReport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TournamentReportsType::class, $tournamentReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tournament_reports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tournament_reports/edit.html.twig', [
            'tournament_report' => $tournamentReport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournament_reports_delete', methods: ['POST'])]
    public function delete(Request $request, TournamentReports $tournamentReport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tournamentReport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tournamentReport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tournament_reports_index', [], Response::HTTP_SEE_OTHER);
    }
}
