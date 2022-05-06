<?php

namespace App\Controller;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\Matches;
use App\Entity\JoinRequests;
use App\Form\MatchesType;
use App\Entity\Users;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/matches')]
class MatchesController extends AbstractController
{
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    #[Route('/', name: 'app_matches_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $matches = $entityManager
            ->getRepository(Matches::class)
            ->findAll();

        return $this->render('matches/index.html.twig', [
            'matches' => $matches,
        ]);
    }

    #[Route('/json/list', name: 'app_matches_json_list', methods: ['GET'])]
    public function ListJson(
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer
    ): Response {
        $matches = $entityManager->getRepository(Matches::class)->findAll();
        $jsonContent = $normalizer->normalize($matches, 'json', [
            'groups' => 'post:read',
        ]);
        // return $this->render('games/index.html.twig', [
        //   'games' => $games,
        //]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/json/list/{id}', name: 'app_matches_list_id', methods: ['GET'])]
    public function showId(
        Request $request,
        $id,
        NormalizerInterface $normalizer
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $match = $em->getRepository(Matches::class)->find($id);
        $jsonContent = $normalizer->normalize($match, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/json/new', name: 'app_matches_json_new', methods: ['GET', 'POST'])]
    public function newJson(
        Request $request,
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $match = new Matches();
        $match->setStartTime($request->get('start_time'));
        $match->setRound($request->get(''));
        $match->setTournament($entityManager->getRepository(Tournaments::class)->find($request->get('tournament_id')));
        $match->setTeam1($entityManager->getRepository(Teams::class)->find($request->get('team1_id')));
        $match->setTeam2($entityManager->getRepository(Teams::class)->find($request->get('team2_id')));
        $match->setWinnerTeam($entityManager->getRepository(Teams::class)->find($request->get('winner_team_id')));
        
        $em->persist($match);
        $em->flush();
        $jsonContent = $normalizer->normalize($match, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/new', name: 'app_matches_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $match = new Matches();
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

            return $this->redirectToRoute('app_matches_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('matches/new.html.twig', [
            'match' => $match,
            'form' => $form,
        ]);
    }
     /**
     * @Route("/verify/match", name="match_email")
     */
    public function TournamentEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_tournaments_show');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_tournaments_show');
    }

    #[Route('/{id}', name: 'app_matches_show', methods: ['GET'])]
    public function show(Matches $match): Response
    {
        return $this->render('matches/show.html.twig', [
            'match' => $match,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_matches_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Matches $match, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatchesType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_matches_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('matches/edit.html.twig', [
            'match' => $match,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_matches_delete', methods: ['POST'])]
    public function delete(Request $request, Matches $match, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $match->getId(), $request->request->get('_token'))) {
            $entityManager->remove($match);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_matches_index', [], Response::HTTP_SEE_OTHER);
    }
}