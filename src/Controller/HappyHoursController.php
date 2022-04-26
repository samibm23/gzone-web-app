<?php

namespace App\Controller;

use App\Entity\HappyHours;
use App\Form\HappyHoursType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/happy-hours')]
class HappyHoursController extends AbstractController
{
    #[Route('/', name: 'app_happy_hours_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $happyHours = $entityManager
            ->getRepository(HappyHours::class)
            ->findAll();

        return $this->render('happy_hours/index.html.twig', [
            'happy_hours' => $happyHours,
        ]);
    }



    #[Route('/new', name: 'app_happy_hours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $happyHour = new HappyHours();
        $form = $this->createForm(HappyHoursType::class, $happyHour);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($happyHour);
            $entityManager->flush();

            return $this->redirectToRoute('app_happy_hours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('happy_hours/new.html.twig', [
            'happy_hour' => $happyHour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_happy_hours_show', methods: ['GET'])]
    public function show(HappyHours $happyHour): Response
    {
        return $this->render('happy_hours/show.html.twig', [
            'happy_hour' => $happyHour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_happy_hours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HappyHours $happyHour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HappyHoursType::class, $happyHour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_happy_hours_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('happy_hours/edit.html.twig', [
            'happy_hour' => $happyHour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_happy_hours_delete', methods: ['POST'])]
    public function delete(Request $request, HappyHours $happyHour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$happyHour->getId(), $request->request->get('_token'))) {
            $entityManager->remove($happyHour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_happy_hours_index', [], Response::HTTP_SEE_OTHER);
    }
}
