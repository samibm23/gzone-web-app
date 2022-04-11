<?php

namespace App\Controller;

use App\Entity\PostReports;
use App\Form\PostReportsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post-reports')]
class PostReportsController extends AbstractController
{
    #[Route('/', name: 'app_post_reports_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $postReports = $entityManager
            ->getRepository(PostReports::class)
            ->findAll();

        return $this->render('post_reports/index.html.twig', [
            'post_reports' => $postReports,
        ]);
    }

    #[Route('/new', name: 'app_post_reports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $postReport = new PostReports();
        $form = $this->createForm(PostReportsType::class, $postReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($postReport);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_reports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post_reports/new.html.twig', [
            'post_report' => $postReport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_reports_show', methods: ['GET'])]
    public function show(PostReports $postReport): Response
    {
        return $this->render('post_reports/show.html.twig', [
            'post_report' => $postReport,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_reports_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PostReports $postReport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostReportsType::class, $postReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_reports_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post_reports/edit.html.twig', [
            'post_report' => $postReport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_reports_delete', methods: ['POST'])]
    public function delete(Request $request, PostReports $postReport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$postReport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($postReport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_reports_index', [], Response::HTTP_SEE_OTHER);
    }
}
