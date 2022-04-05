<?php

namespace App\Controller;

use App\Entity\StoreReports;
use App\Form\StoreReportsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/store/reports')]
class StoreReportsController extends AbstractController
{
    #[Route('/', name: 'app_store_reports_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $storeReports = $entityManager
            ->getRepository(StoreReports::class)
            ->findAll();

        return $this->render('store_reports/index.html.twig', [
            'store_reports' => $storeReports,
        ]);
    }

    #[Route('/new', name: 'app_store_reports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $storeReport = new StoreReports();
        $form = $this->createForm(StoreReportsType::class, $storeReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($storeReport);
            $entityManager->flush();

            return $this->redirectToRoute('app_store_reports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('store_reports/new.html.twig', [
            'store_report' => $storeReport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_store_reports_show', methods: ['GET'])]
    public function show(StoreReports $storeReport): Response
    {
        return $this->render('store_reports/show.html.twig', [
            'store_report' => $storeReport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_store_reports_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StoreReports $storeReport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StoreReportsType::class, $storeReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_store_reports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('store_reports/edit.html.twig', [
            'store_report' => $storeReport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_store_reports_delete', methods: ['POST'])]
    public function delete(Request $request, StoreReports $storeReport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$storeReport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($storeReport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_store_reports_index', [], Response::HTTP_SEE_OTHER);
    }
}
