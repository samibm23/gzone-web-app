<?php

namespace App\Controller;

use App\Entity\JoinRequests;
use App\Form\JoinRequestsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/join-requests')]
class JoinRequestsController extends AbstractController
{
    #[Route('/', name: 'app_join_requests_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $joinRequests = $entityManager
            ->getRepository(JoinRequests::class)
            ->findAll();

        return $this->render('join_requests/index.html.twig', [
            'join_requests' => $joinRequests,
        ]);
    }

    #[Route('/new', name: 'app_join_requests_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $joinRequest = new JoinRequests();
        $form = $this->createForm(JoinRequestsType::class, $joinRequest);
        $form->handleRequest($request);
        $date = new \DateTime('now'); 
        $joinRequest->setRequestDate($date);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($joinRequest);
            $entityManager->flush();

            return $this->redirectToRoute('app_join_requests_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('join_requests/new.html.twig', [
            'join_request' => $joinRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_join_requests_show', methods: ['GET'])]
    public function show(JoinRequests $joinRequest): Response
    {
        return $this->render('join_requests/show.html.twig', [
            'join_request' => $joinRequest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_join_requests_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, JoinRequests $joinRequest, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JoinRequestsType::class, $joinRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_join_requests_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('join_requests/edit.html.twig', [
            'join_request' => $joinRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_join_requests_delete', methods: ['POST'])]
    public function delete(Request $request, JoinRequests $joinRequest, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$joinRequest->getId(), $request->request->get('_token'))) {
            $entityManager->remove($joinRequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_join_requests_index', [], Response::HTTP_SEE_OTHER);
    }
}
