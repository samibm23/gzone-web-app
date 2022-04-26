<?php

namespace App\Controller;

use App\Entity\Badges;
use App\Form\BadgesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/badges')]
class BadgesController extends AbstractController
{
    #[Route('/', name: 'app_badges_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $badges = $entityManager
            ->getRepository(Badges::class)
            ->findAll();

        return $this->render('badges/index.html.twig', [
            'badges' => $badges,
        ]);
    }


    #[Route('/new', name: 'app_badges_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $badge = new Badges();
        $form = $this->createForm(BadgesType::class, $badge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($badge);
            $entityManager->flush();

            return $this->redirectToRoute('app_badges_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('badges/new.html.twig', [
            'badge' => $badge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_badges_show', methods: ['GET'])]
    public function show(Badges $badge): Response
    {
        return $this->render('badges/show.html.twig', [
            'badge' => $badge,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_badges_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Badges $badge, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BadgesType::class, $badge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_badges_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('badges/edit.html.twig', [
            'badge' => $badge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_badges_delete', methods: ['POST'])]
    public function delete(Request $request, Badges $badge, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$badge->getId(), $request->request->get('_token'))) {
            $entityManager->remove($badge);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_badges_index', [], Response::HTTP_SEE_OTHER);
    }


}
