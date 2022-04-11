<?php

namespace App\Controller;

use App\Entity\MarketItemReports;
use App\Form\MarketItemReportsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/market-item-reports')]
class MarketItemReportsController extends AbstractController
{
    #[Route('/', name: 'app_market_item_reports_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $marketItemReports = $entityManager
            ->getRepository(MarketItemReports::class)
            ->findAll();

        return $this->render('market_item_reports/index.html.twig', [
            'market_item_reports' => $marketItemReports,
        ]);
    }

    #[Route('/new', name: 'app_market_item_reports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $marketItemReport = new MarketItemReports();
        $form = $this->createForm(MarketItemReportsType::class, $marketItemReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($marketItemReport);
            $entityManager->flush();

            return $this->redirectToRoute('app_market_item_reports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_item_reports/new.html.twig', [
            'market_item_report' => $marketItemReport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_item_reports_show', methods: ['GET'])]
    public function show(MarketItemReports $marketItemReport): Response
    {
        return $this->render('market_item_reports/show.html.twig', [
            'market_item_report' => $marketItemReport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_market_item_reports_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MarketItemReports $marketItemReport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MarketItemReportsType::class, $marketItemReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_market_item_reports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_item_reports/edit.html.twig', [
            'market_item_report' => $marketItemReport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_item_reports_delete', methods: ['POST'])]
    public function delete(Request $request, MarketItemReports $marketItemReport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marketItemReport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($marketItemReport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_market_item_reports_index', [], Response::HTTP_SEE_OTHER);
    }
}
