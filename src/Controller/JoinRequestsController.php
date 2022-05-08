<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Entity\Tournaments;
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
#[Route('/t/{team_id}/{invitation}/{message}/{tournament_id}', name: 'app_tournament_join_requests_new', methods: ['GET', 'POST'])]
    public function Tournament(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (count($entityManager->getRepository(JoinRequests::class)->findBy([
            "team" => $entityManager->getRepository(Teams::class)->find((int)$request->get("team_id")),
            "tournament" => $entityManager->getRepository(Tournaments::class)->find((int)$request->get("tournament_id")),
            ])) > 0) {
                $entityManager->remove($entityManager->getRepository(JoinRequests::class)->findBy([
                    "team" => $entityManager->getRepository(Teams::class)->find((int)$request->get("team_id")),
                    "tournament" => $entityManager->getRepository(Tournaments::class)->find((int)$request->get("tournament_id")),
                    ])[0]);
                $entityManager->flush();
        } else {
            $joinRequest = new JoinRequests();
            $joinRequest->setMessage($request->get("message"));
            $joinRequest->setRequestDate(new \DateTime('now'));
            $joinRequest->setAccepted(false);
            $joinRequest->setInvitation((boolean)$request->get("invitation"));
            $joinRequest->setTournament($entityManager->getRepository(Tournaments::class)->find((int)$request->get("tournament_id")));
            $joinRequest->setTeam($entityManager->getRepository(Teams::class)->find((int)$request->get("team_id")));

            if (
                $joinRequest->getTournament()->getRequiredTeams() > count($entityManager->getRepository(JoinRequests::class)->findBy([
                    "tournament" => $joinRequest->getTournament(),
                    "accepted" => true
                ]))
                && (
                    $joinRequest->getInvitation() && $this->getUser()->getId() == $joinRequest->getTournament()->getAdmin()->getId() && $joinRequest->getTeam()->getInvitable()
                    || !$joinRequest->getInvitation() && $this->getUser()->getId() == $joinRequest->getTeam()?->getAdmin()->getId() && $joinRequest->getTournament()->getRequestable()
                )
                && $joinRequest->getTeam()->getTeamSize() == $joinRequest->getTournament()->getTeamSize()
                && $joinRequest->getTeam()->getGame()?->getId() == $joinRequest->getTournament()->getGame()?->getId()
            ) {
                $entityManager->persist($joinRequest);
                $entityManager->flush();
            }
        }
        if ((boolean) $request->get("invitation")) {
            return $this->redirectToRoute("app_teams_show", ["id" => $joinRequest->getTeam()->getId()]);
        } else {
            return $this->redirectToRoute("app_tournaments_show", ["id" => $joinRequest->getTournament()->getId()]);
        }
    }

    #[Route('/', name: 'app_join_requests_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT a FROM App\Entity\JoinRequests a 
            ORDER BY a.message ASC'
        );
    
        $joinRequests = $query->getResult();
        return $this->render('join_requests/index.html.twig',
        array('join_requests' => $joinRequests));
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