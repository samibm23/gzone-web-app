<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Games;
use App\Entity\Matches;
use App\Entity\Tournaments;
use App\Entity\Teams;
use App\Entity\JoinRequests;
use App\Form\TournamentsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    #[Route('/json/list', name: 'app_tournaments_json_list', methods: ['GET'])]
    public function ListJson(
        EntityManagerInterface $entityManager
    ): Response {
        $tournaments = $entityManager->getRepository(Tournaments::class)->findAll();
	  $encoders = [new JsonEncoder()];
	  $normalizers = [new ObjectNormalizer()];

	  $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($tournaments, 'json', [
            'groups' => 'post:read',
        ]);
        // return $this->render('games/index.html.twig', [
        //   'games' => $games,
        //]);
        return new Response($jsonContent);
    }

    #[Route('/json/list/{id}', name: 'app_tournaments_json_list_show', methods: ['GET'])]
    public function showId(
        Request $request,
        $id,
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $tournament = $em->getRepository(Tournaments::class)->find($id);
	  $encoders = [new JsonEncoder()];
	  $normalizers = [new ObjectNormalizer()];

	  $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($tournament, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response($jsonContent);
    }

    #[Route('/json/new', name: 'app_tournaments_json_new', methods: ['GET', 'POST'])]
    public function newJson(
        Request $request,
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $tournament = new Tournaments();
        $tournament->setName($request->get('name'));
        $tournament->setDescription($request->get('description'));
        $tournament->setRequiredTeams((int)$request->get('required_teams'));
        $tournament->setTeamSize((int)$request->get('team_size'));
        $tournament->setRequestable((boolean)$request->get('requestable'));
        $tournament->setApproved((boolean)$request->get('approved'));
        $tournament->setCreateDate(new \DateTime('now'));
        $tournament->setAdmin($entityManager->getRepository(Users::class)->find((int)$request->get('admin_id')));
        $tournament->setGame($entityManager->getRepository(Games::class)->find((int)$request->get('game_id')));
        
        $em->persist($tournament);
        $em->flush();
        $jsonContent = $normalizer->normalize($tournament, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/new', name: 'app_tournaments_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tournament = new Tournaments();
        $tournament->setAdmin($this->getUser());
        $tournament->setCreateDate(new \DateTime('now'));
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
    public function show(Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        $matches = $entityManager->getRepository(Matches::class)->findBy(['tournament' => $tournament]);
        $joinedTeams = count($entityManager->getRepository(JoinRequests::class)->findBy([
            "tournament" => $tournament,
            "accepted" => true
        ]));

        return $this->render('tournaments/show.html.twig', [
            'tournament' => $tournament,
            'matches' => $matches,
            'joinedTeams' => $joinedTeams,
            'teams' => ($tournament->getRequiredTeams() - $joinedTeams == 0)? null : $entityManager->getRepository(Teams::class)->findBy([
                "admin" => $this->getUser(),
                "teamSize" => $tournament->getTeamSize(),
                "game" => $tournament->getGame()
            ]),
            'requestingTeamId' => $entityManager->getRepository(Teams::class)->findOneBy([
                "admin" => $this->getUser(),
                "teamSize" => $tournament->getTeamSize(),
                "game" => $tournament->getGame()
            ])?->getId()
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
