<?php

namespace App\Controller;

use App\Entity\UserGamePreferences;
use App\Form\UserGamePreferencesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/game/preferences')]
class UserGamePreferencesController extends AbstractController
{
    #[Route('/', name: 'app_user_game_preferences_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $userGamePreferences = $entityManager
            ->getRepository(UserGamePreferences::class)
            ->findAll();

        return $this->render('user_game_preferences/index.html.twig', [
            'user_game_preferences' => $userGamePreferences,
        ]);
    }

    #[Route('/new', name: 'app_user_game_preferences_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userGamePreference = new UserGamePreferences();
        $form = $this->createForm(UserGamePreferencesType::class, $userGamePreference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userGamePreference);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_game_preferences_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_game_preferences/new.html.twig', [
            'user_game_preference' => $userGamePreference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_game_preferences_show', methods: ['GET'])]
    public function show(UserGamePreferences $userGamePreference): Response
    {
        return $this->render('user_game_preferences/show.html.twig', [
            'user_game_preference' => $userGamePreference,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_game_preferences_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserGamePreferences $userGamePreference, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserGamePreferencesType::class, $userGamePreference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_game_preferences_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_game_preferences/edit.html.twig', [
            'user_game_preference' => $userGamePreference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_game_preferences_delete', methods: ['POST'])]
    public function delete(Request $request, UserGamePreferences $userGamePreference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userGamePreference->getId(), $request->request->get('_token'))) {
            $entityManager->remove($userGamePreference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_game_preferences_index', [], Response::HTTP_SEE_OTHER);
    }
}
