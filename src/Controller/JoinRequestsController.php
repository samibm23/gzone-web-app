<?php

namespace App\Controller;

use App\Entity\JoinRequests;
use App\Form\JoinRequestsType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
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
            if (
                count($entityManager->getRepository(JoinRequests::class)->findBy([
                    "user" => $joinRequest->getUser(),
                    "team" => $joinRequest->getTeam(),
                    "tournament" => $joinRequest->getTournament()
                ])) == 0
                && (
                    (
                        $joinRequest->getInvitation() == true
                        && (
                            $joinRequest->getUser()?->getInvitable()
                            || $joinRequest->getTeam()?->getInvitable()
                        )
                        && (
                            $joinRequest->getTeam()?->getAdmin()->getId() == $this->getUser()->getId()
                            || $joinRequest->getTournament()?->getAdmin()->getId() == $this->getUser()->getId()
                        )
                    )
                    || (
                        $joinRequest->getInvitation() == false
                        && (
                            $joinRequest->getTeam()?->getRequestable()
                            || $joinRequest->getTournament()?->getRequestable()
                            )
                        )
                        && (
                            $joinRequest->getUser()?->getId() == $this->getUser()->getId()
                            || $joinRequest->getTeam()->getAdmin()->getId() == $this->getUser()->getId()
                        )
                    )
                && (
                    $joinRequest->getUser() != null
                    || (
                        $joinRequest->getTeam()->getTeamSize() == $joinRequest->getTournament()->getTeamSize()
                        && $joinRequest->getTeam()->getGame()->getId() == $joinRequest->getTournament()->getGame()->getId()
                    )
                )
                && (
                    count($entityManager->getRepository(JoinRequests::class)->matching(Criteria::create()
                        ->where(Criteria::expr()->neq('user', null))
                        ->andWhere(Criteria::expr()->eq('accepted', true))
                    )) < $joinRequest->getTeam()?->getTeamSize()
                    || count($entityManager->getRepository(JoinRequests::class)->matching(Criteria::create()
                        ->where(Criteria::expr()->neq('tournament', null))
                        ->andWhere(Criteria::expr()->eq('accepted', true))
                    )) < $joinRequest->getTournament()?->getRequiredTeams()
                )
            ) {
                $entityManager->persist($joinRequest);
                $entityManager->flush();
            }

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
