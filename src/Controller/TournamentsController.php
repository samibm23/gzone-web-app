<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Games;
use App\Entity\Matches;
use App\Entity\Tournaments;
use App\Entity\Teams;
use App\Entity\JoinRequests;
use App\Form\TournamentsType;
use App\Form\MatchesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/tournaments')]
class TournamentsController extends AbstractController
{
    #[Route('/json', name: 'app_tournaments_json_index', methods: ['GET'])]
    public function indexJson(
        EntityManagerInterface $entityManager
    ): Response {
        $tournaments = $entityManager->getRepository(Tournaments::class)->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($tournaments, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response($jsonContent);
    }

    #[Route('/{id}/matches/json', name: 'app_tournament_matches_json_index', methods: ['GET'])]
    public function indexMatchesJson(
        EntityManagerInterface $entityManager,
        Tournaments $tournament
    ): Response {
        $matches = $entityManager->getRepository(Matches::class)->findBy(['tournament' => $tournament]);
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($matches, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response($jsonContent);
    }

    #[Route('/{id}/matches/json/new', name: 'app_tournament_matches_json_new', methods: ['GET', 'POST'])]
    public function newMatchJson(
        Request $request,
        EntityManagerInterface $entityManager,
        Tournaments $tournament
    ): Response {
        $match = new Matches();
        $match->setTournament($tournament);
        $match->setStartTime($request->get('start_time'));
        $match->setRound($request->get('round'));
        $match->setTournament($entityManager->getRepository(Tournaments::class)->find((int)$request->get('tournament_id')));
        $match->setTeam1($entityManager->getRepository(Teams::class)->find((int)$request->get('team1')));
        $match->setTeam2($entityManager->getRepository(Teams::class)->find((int)$request->get('team2')));
        $match->setWinnerTeam($entityManager->getRepository(Teams::class)->find((int)$request->get('winner_team')));

        $entityManager->persist($match);
        $entityManager->flush();

        return new Response("success");
    }

    #[Route('/json/new', name: 'app_tournaments_json_new', methods: ['GET', 'POST'])]
    public function newJson(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $tournament = new Tournaments();
        $tournament->setName($request->get('name'));
        $tournament->setDescription($request->get('description'));
        $tournament->setRequiredTeams((int)$request->get('required_teams'));
        $tournament->setTeamSize((int)$request->get('team_size'));
        $tournament->setRequestable((bool)$request->get('requestable'));
        $tournament->setApproved((bool)$request->get('approved'));
        $tournament->setCreateDate(new \DateTime('now'));
        $tournament->setAdmin($entityManager->getRepository(Users::class)->find((int)$request->get('admin_id')));
        $tournament->setGame($entityManager->getRepository(Games::class)->find((int)$request->get('game_id')));

        $entityManager->persist($tournament);
        $entityManager->flush();

        return new Response(json_encode("Success"));
    }

    #[Route('/json/edit/{id}', name: 'app_tournaments_json_update', methods: ['GET', 'POST'])]
    public function updateJson(Request $request, EntityManagerInterface $entityManager, Tournaments $tournament): Response
    {
        if ($request->get('name') != null) $tournament->setName($request->get('name'));
        if ($request->get('description') != null) $tournament->setDescription($request->get('description'));
        if ($request->get('required_teams') != null) $tournament->setRequiredTeams((int)$request->get('required_teams'));
        if ($request->get('team_size') != null) $tournament->setTeamSize((int)$request->get('team_size'));
        if ($request->get('requestable') != null) $tournament->setRequestable((bool)$request->get('requestable'));
        if ($request->get('approved') != null) $tournament->setApproved((bool)$request->get('approved'));

        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($tournament, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response("Information update" . $jsonContent);
    }

    #[Route('/json/delete/{id}', name: 'app_tournaments_json_delete', methods: ['GET', 'POST'])]
    public function deleteJson(Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($tournament);
        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($tournament, 'json', [
            'groups' => 'post:read',
        ]);        return new Response("Tournament deleted" . $jsonContent);
    }

    #[Route('/json/{id}', name: 'app_tournaments_json_show', methods: ['GET'])]
    public function showJson(
        Tournaments $tournament
    ): Response {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($tournament, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response($jsonContent);
    }
    
    #[Route('/', name: 'app_tournaments_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $tournaments = $entityManager
            ->getRepository(Tournaments::class)
            ->findAll();
        $tournaments = $paginator->paginate(
            // Doctrine Query, not results
            $tournaments,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            3
        );
        return $this->render('tournaments/index.html.twig', [
            'tournaments' => $tournaments,
            'userId' => $this->getUser()->getId()
        ]);
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

    #[Route('/{id}/matches/new', name: 'app_tournaments_matches_new', methods: ['GET', 'POST'])]
    public function newMatch(Request $request, EntityManagerInterface $entityManager, Tournaments $tournament): Response
    {
        $user = $this->getUser();
        $match = new Matches();
        $match->setTournament($tournament);
        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (
                $match->getWinnerTeam() == null
                && $match->getTeam1()->getId() != $match->getTeam2()->getId()
                && count($entityManager->getRepository(JoinRequests::class)->findBy([
                    "team" => $match->getTeam1(),
                    "tournament" => $match->getTournament(),
                    "accepted" => true
                ])) == 1
                && count($entityManager->getRepository(JoinRequests::class)->findBy([
                    "team" => $match->getTeam2(),
                    "tournament" => $match->getTournament(),
                    "accepted" => true
                ])) == 1
            ) {
                $entityManager->persist($match);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_tournaments_show', ["id" => $tournament->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('matches/new.html.twig', [
            'tournamentId' => $tournament->getId(),
            'match' => $match,
            'form' => $form,
        ]);
    }

    #[Route('/{tid}/matches/{mid}', name: 'app_tournaments_matches_show', methods: ['GET'])]
    public function showMatch(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render('matches/show.html.twig', [
            'userId' => $this->getUser()->getId(),
            'tournament' => $entityManager->getRepository(Tournaments::class)->find((int) $request->get('tid')),
            'match' => $entityManager->getRepository(Matches::class)->find((int)$request->get('mid')),
        ]);
    }

    #[Route('/{tid}/matches/{mid}/edit', name: 'app_tournaments_matches_edit', methods: ['GET', 'POST'])]
    public function editMatch(Request $request, EntityManagerInterface $entityManager): Response
    {
        $match = $entityManager->getRepository(Matches::class)->find((int)$request->get('mid'));
        $tournament = $entityManager->getRepository(Tournaments::class)->find((int)$request->get('tid'));
        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_matches_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('matches/edit.html.twig', [
            'tournament' => $tournament,
            'match' => $match,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournaments_show', methods: ['GET'])]
    public function show(Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        $matches = $entityManager->getRepository(Matches::class)->findBy(['tournament' => $tournament], ['round' => 'ASC', 'startTime' => 'ASC']);
        $joinedTeams = count($entityManager->getRepository(JoinRequests::class)->findBy([
            "tournament" => $tournament,
            "accepted" => true
        ]));

        $team = $entityManager->getRepository(Teams::class)->findOneBy([
            "admin" => $this->getUser(),
            "teamSize" => $tournament->getTeamSize(),
            "game" => $tournament->getGame()
        ]);

        return $this->render('tournaments/show.html.twig', [
            'userId' => $this->getUser()->getId(),
            'tournament' => $tournament,
            'matches' => $matches,
            'joinedTeams' => $joinedTeams,
            'jr' => $entityManager->getRepository(JoinRequests::class)->findOneBy(['team' => $team, 'tournament' => $tournament]),
            'teams' => ($tournament->getRequiredTeams() - $joinedTeams == 0) ? null : $entityManager->getRepository(Teams::class)->findBy([
                "admin" => $this->getUser(),
                "teamSize" => $tournament->getTeamSize(),
                "game" => $tournament->getGame()
            ]),
            'requestingTeamId' => $team?->getId()
        ]);
    }
    #[Route('/{id}/accept-join-request/{jrid}', name: 'app_join_requests_accept_tournament', methods: ['GET', 'POST'])]
    public function acceptTournament(Request $request, EntityManagerInterface $entityManager): Response
    {
        $joinRequest = $entityManager->getRepository(JoinRequests::class)->find((int)$request->get('jrid'));
        $joinRequest->setAccepted(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_tournaments_edit', [
            'id' => $joinRequest->getTournament()->getId()
        ]);
    }
    #[Route('/{id}/decline-join-request/{jrid}', name: 'app_join_requests_decline_tournament', methods: ['GET', 'POST'])]
    public function declineTournament(Request $request, EntityManagerInterface $entityManager): Response
    {
        $joinRequest = $entityManager->getRepository(JoinRequests::class)->find((int)$request->get('jrid'));
        $joinRequest->setAccepted(false);
        $entityManager->flush();

        return $this->redirectToRoute('app_tournaments_edit', [
            'id' => $joinRequest->getTournament()->getId()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tournaments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        $joinRequests = $entityManager
            ->getRepository(JoinRequests::class)
            ->findBy(['tournament' => $tournament]);
        $form = $this->createForm(TournamentsType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tournaments/edit.html.twig', [
            'tournament' => $tournament,
            'join_requests' => $joinRequests,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournaments_delete', methods: ['POST'])]
    public function delete(Request $request, Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tournament->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tournament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
    }
}
