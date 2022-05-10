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
    #[Route('/', name: 'app_tournaments_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,Request $request, PaginatorInterface $paginator): Response
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
            )
            {
                $entityManager->persist($match);
                $entityManager->flush();

                foreach($entityManager->getRepository(JoinRequests::class)->findBy([
                    "accepted" => true,
                    "tournament" => null,
                    "team" => $match->getTeam1()
                ]) as $jr) {
                    // generate a signed url and email it to the user
                    $this->emailVerifier->sendEmailConfirmation(
                        'tournament_email',
                        $user,
                        (new TemplatedEmail())
                            ->from(new Address('appgzone@gmail.com', 'Gzone App'))
                            ->to($jr->getUser()->getEmail())
                            ->subject('Please Confirm your Email')
                            ->htmlTemplate('TournamentConfirmation/confirmation_TR.html.twig')
                    );
                }
                foreach($entityManager->getRepository(JoinRequests::class)->findBy([
                    "accepted" => true,
                    "tournament" => null,
                    "team" => $match->getTeam2()
                ]) as $jr) {
                    // generate a signed url and email it to the user
                    $this->emailVerifier->sendEmailConfirmation(
                        'tournament_email',
                        $user,
                        (new TemplatedEmail())
                            ->from(new Address('appgzone@gmail.com', 'Gzone App'))
                            ->to($jr->getUser()->getEmail())
                            ->subject('Please Confirm your Email')
                            ->htmlTemplate('TournamentConfirmation/confirmation_TR.html.twig')
                    );
                }
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
        $matches = $entityManager->getRepository(Matches::class)->findBy(['tournament' => $tournament], ['startTime' => 'ASC']);
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
            'teams' => ($tournament->getRequiredTeams() - $joinedTeams == 0)? null : $entityManager->getRepository(Teams::class)->findBy([
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
        $joinRequest=$entityManager->getRepository(JoinRequests::class)->find((int)$request->get('jrid'));
        $joinRequest->setAccepted(true);
        $entityManager->flush();

       return $this->redirectToRoute('app_tournaments_edit', [
            'id'=>$joinRequest->getTournament()->getId()
        ]);
    }
    #[Route('/{id}/decline-join-request/{jrid}', name: 'app_join_requests_decline_tournament', methods: ['GET', 'POST'])]
    public function declineTournament(Request $request, EntityManagerInterface $entityManager): Response
    {
        $joinRequest=$entityManager->getRepository(JoinRequests::class)->find((int)$request->get('jrid'));
        $joinRequest->setAccepted(false);
        $entityManager->flush();

       return $this->redirectToRoute('app_tournaments_edit', [
            'id'=>$joinRequest->getTournament()->getId()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tournaments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        $joinRequests = $entityManager
        ->getRepository(JoinRequests::class)
        ->findBy(['tournament'=>$tournament]);
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
        if ($this->isCsrfTokenValid('delete'.$tournament->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tournament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
    }
}
