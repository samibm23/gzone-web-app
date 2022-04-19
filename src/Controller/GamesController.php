<?php

namespace App\Controller;

use App\Entity\Games;
use App\Form\GamesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/games')]
class GamesController extends AbstractController
{
    #[Route('/', name: 'app_games_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $games = $entityManager
            ->getRepository(Games::class)
            ->findAll();

        return $this->render('games/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/new', name: 'app_games_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Games();
        $form = $this->createForm(GamesType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ImageFile = $form->get('photo_url')->getData();
            if ($ImageFile) {

                // this is needed to safely include the file name as part of the URL

                $newFilename = md5(uniqid()).'.'.$ImageFile->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/games';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $game->setPhotoUrl($newFilename);


            }

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('games/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_games_show', methods: ['GET'])]
    public function show(Games $game): Response
    {
        return $this->render('games/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_games_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Games $game, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GamesType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ImageFile = $form->get('photo_url')->getData();
            if ($ImageFile) {
                $newFilename = md5(uniqid()).'.'.$ImageFile->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/images/games';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $game->setPhotoUrl($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('app_games_index');
        }

        return $this->render('games/edit.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_games_delete', methods: ['POST'])]
    public function delete(Request $request, Games $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
    }

   

}
