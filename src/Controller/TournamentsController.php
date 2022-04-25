<?php

namespace App\Controller;

use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use App\Entity\Tournaments;
use App\Entity\Users;
use App\Form\TournamentsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/tournaments')]
class TournamentsController extends AbstractController
{
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
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

    #[Route('/new', name: 'app_tournaments_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {        
        $user = $this->getUser(); 
        $tournament = new Tournaments();
        $form = $this->createForm(TournamentsType::class, $tournament);
        $form->handleRequest($request);
        $date = new \DateTime('now'); 
        $tournament->setCreateDate($date);
        if ($form->isSubmitted() && $form->isValid()) {           
            $entityManager->persist($tournament);
            $entityManager->flush();
             // generate a signed url and email it to the user
             $this->emailVerifier->sendEmailConfirmation('tournament_email', $user,
             (new TemplatedEmail())
                 ->from(new Address('appgzone@gmail.com', 'Gzone App'))
                 ->to('mahdi3soussi@gmail.com')
                 ->subject('Please Confirm your Email')
                 ->htmlTemplate('TournamentConfirmation/confirmation_TR.html.twig')
         );            
            return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('tournaments/new.html.twig', [
            'tournament' => $tournament,
            'form' => $form,
        ]);
    }
 /**
     * @Route("/verify/tournament", name="tournament_email")
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
    #[Route('/{id}', name: 'app_tournaments_show', methods: ['GET'])]
    public function show(Tournaments $tournament): Response
    {
        return $this->render('tournaments/show.html.twig', [
            'tournament' => $tournament,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tournaments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tournaments $tournament, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getId() != $tournament->getAdmin()->getId()) {
            return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
        }
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
        if ($this->getUser()->getId() != $tournament->getAdmin()->getId()) {
            return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
        }
        
        if ($this->isCsrfTokenValid('delete'.$tournament->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tournament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tournaments_index', [], Response::HTTP_SEE_OTHER);
    }
}
