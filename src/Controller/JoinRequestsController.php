<?php

namespace App\Controller;

use App\Entity\Users;
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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/join-requests')]
class JoinRequestsController extends AbstractController
{
    
    #[Route('/json', name: 'app_join_requests_json_index', methods: ['GET'])]
    public function indexJson(
        EntityManagerInterface $entityManager
    ): Response {
        $joinRequests = $entityManager->getRepository(JoinRequests::class)->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($joinRequests, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response($jsonContent);
    }
    #[Route('/json/new', name: 'app_join_requests_json_new', methods: ['GET', 'POST'])]
    public function newJson(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $joinRequest = new JoinRequests();
        $joinRequest->setMessage($request->get('message'));
        $joinRequest->setInvitation($request->get('invitation'));
        $joinRequest->setUser($entityManager->getRepository(Users::class)->find((int)$request->get("user")));
        $joinRequest->setTeam($entityManager->getRepository(Teams::class)->find((int)$request->get("team_id")));
        $joinRequest->setTournament($entityManager->getRepository(Tournaments::class)->find((int)$request->get("tournament_id")));

        $date = new \DateTime('now');
        $joinRequest->setRequestDate($date);
        $entityManager->persist($joinRequest);
        $entityManager->flush();

        return new Response(json_encode("Success"));
    }

    #[Route('/json/{id}', name: 'app_join_requests_json_show', methods: ['GET'])]
    public function showJson(
        JoinRequest $joinRequest
    ): Response {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($joinRequest, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response($jsonContent);
    }



    #[Route('/json/edit/{id}', name: 'app_join_requests_json_update', methods: ['GET', 'POST'])]
    public function updateJson(Request $request, EntityManagerInterface $entityManager, JoinRequests $joinRequest): Response
    
    {
        if ($request->get('invitation') != null) $joinRequest->setInvitation($request->get('invitation'));


        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($joinRequest, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response("Information update" . $jsonContent);
    }

    #[Route('/json/delete/{id}', name: 'app_join_requests_json_delete', methods: ['GET', 'POST'])]
    public function deleteJson(JoinRequests $joinRequest, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($joinRequest);
        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($joinRequest, 'json', [
            'groups' => 'post:read',
        ]);       
         return new Response(" deleted" . $jsonContent);
    }
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
            return $this->redirectToRoute("app_join_requests_show", ["id" =>(int) $request->get("team_id")]);
        } else {
            return $this->redirectToRoute("app_tournaments_show", ["id" => (int) $request->get("tournament_id")]);
        }
    }
    

    #[Route('/u/{user_id}/{invitation}/{message}/{team_id}', name: 'app_user_join_requests_new', methods: ['GET', 'POST'])]
    public function User(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (count($entityManager->getRepository(JoinRequests::class)->findBy([
            "user" => $entityManager->getRepository(Users::class)->find((int)$request->get("user_id")),
            "team" => $entityManager->getRepository(Teams::class)->find((int)$request->get("team_id")),
            ])) > 0) {
                $entityManager->remove($entityManager->getRepository(JoinRequests::class)->findBy([
                    "user" => $entityManager->getRepository(Users::class)->find((int)$request->get("user_id")),
                    "team" => $entityManager->getRepository(Teams::class)->find((int)$request->get("team_id")),
                    ])[0]);
                $entityManager->flush();
        } else {
            $joinRequest = new JoinRequests();
            $joinRequest->setMessage($request->get("message"));
            $joinRequest->setRequestDate(new \DateTime('now'));
            $joinRequest->setInvitation((boolean)$request->get("invitation"));
            $joinRequest->setTeam($entityManager->getRepository(Teams::class)->find((int)$request->get("team_id")));
            $joinRequest->setUser($entityManager->getRepository(Users::class)->find((int)$request->get("user_id")));

            if (
                $joinRequest->getTeam()?->getTeamSize() > count($entityManager->getRepository(JoinRequests::class)->findBy([
                    "user" => $joinRequest->getUser(),
                    "accepted" => true
                ]))
                && (
                    $joinRequest->getInvitation() && $this->getUser()?->getId() == $joinRequest->getTeam()?->getAdmin()?->getId() && $joinRequest->getUser()?->getInvitable()
                    || !$joinRequest->getInvitation() && $joinRequest->getTeam()->getRequestable()
                )
                
            ) {
                $entityManager->persist($joinRequest);
                $entityManager->flush();
            }
        }
        if ((boolean) $request->get("invitation")) {
            return $this->redirectToRoute("app_users_show", ["id" => (int) $request->get("user_id")]);
        } else {
            return $this->redirectToRoute("app_join_requests_show", ["id" => (int) $request->get("team_id")]);
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


    #[Route('/{id}/tournament/{accepted}', name: 'app_join_requests_respond_tournament', methods: ['GET', 'POST'])]
    public function editTournament(Request $request, JoinRequests $joinRequest, EntityManagerInterface $entityManager): Response
    {
        $joinRequest->setAccepted((boolean)$request->get('accepted'));
        $entityManager->flush();

       return $this->redirectToRoute('app_tournaments_show', [
            'id'=>$joinRequest->getTournament()->getId()
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