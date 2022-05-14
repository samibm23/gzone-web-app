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

#[Route('/join-requests')]
class JoinRequestsController extends AbstractController
{
<<<<<<< Updated upstream
#[Route('/t/{team_id}/{invitation}/{message}/{tournament_id}', name: 'app_tournament_join_requests_new', methods: ['GET', 'POST'])]
    public function Tournament(Request $request, EntityManagerInterface $entityManager): Response
=======
    
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

  
    #[Route('/u/{user_id}/{invitation}/{message}/{team_id}', name: 'app_user_join_requests_new', methods: ['GET', 'POST'])]
    public function User(Request $request, EntityManagerInterface $entityManager): Response
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
            return $this->redirectToRoute("app_teams_show", ["id" =>(int) $request->get("team_id")]);
=======
            return $this->redirectToRoute("app_users_show", ["id" => (int) $request->get("user_id")]);
>>>>>>> Stashed changes
        } else {
            return $this->redirectToRoute("app_teams_show", ["id" => (int) $request->get("team_id")]);
        }
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
<<<<<<< Updated upstream
            return $this->redirectToRoute("app_teams_show", ["id" => (int) $request->get("team_id")]);
=======
            return $this->redirectToRoute("app_tournaments_show", ["id" => (int) $request->get("tournament_id")]);
>>>>>>> Stashed changes
        }
    }
    

<<<<<<< Updated upstream
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
    
    #[Route('/json/list', name: 'app_joinRequests_json_list', methods: ['GET'])]
    public function ListJson(EntityManagerInterface $entityManager, NormalizerInterface $normalizer): Response
    {
        $joinRequests= $entityManager
            ->getRepository(Teams::class)
            ->findAll();
        $jsonContent = $normalizer->normalize($joinRequests, 'json', ['groups'=>'post:read']);
        // return $this->render('games/index.html.twig', [
        //   'games' => $games,
        //]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/json/list/{id}', name: 'app_joinRequests_json_get', methods: ['GET'])]
    public function showId(Request $request, $id, NormalizerInterface $normalizer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $joinRequest = $em->getRepository(Teams::class)->find($id);
        $jsonContent = $normalizer->normalize($joinRequest, 'json', ['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/json/new', name: 'app_joinRequests_json_new', methods: ['GET', 'POST'])]
    public function newJson(Request $request, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
    {
        $em = $this->getDoctrine()->getManager();
        $team= new Teams();
        $team->setPhotoUrl($request->get('photo_url'));
        $team->setName($request->get('name'));
        $team->setTeamSize($request->get('team_size'));
        $team->setRequestable($request->get('requestable'));
        $team->setInvitable($request->get('invitable'));
        $team->setDescription($request->get('description'));
        $team->setGame($entityManager->getRepository(Games::class)->find((int)$request->get("game_id")));
        $date = new \DateTime('now'); 
        $team->setCreateDate($date);
        $team->setAdmin($entityManager->getRepository(Users::class)->find((int)$request->get("admin_id")));
        $em->persist($team);
        $em->flush();
        $jsonContent = $normalizer->normalize($team, 'json', ['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/json/update/{id}', name: 'app_teams_json_update', methods: ['GET', 'POST'])]
    public function updateJson(Request $request, NormalizerInterface $normalizer,$id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $team= $em->getRepository(Teams::class)->find($id);
        $team->setName($request->get('name'));
        $team->setDescription($request->get('description'));
        $em->persist($team);
        $em->flush();
        $jsonContent = $normalizer->normalize($team, 'json', ['groups'=>'post:read']);
        return new Response("Information update".json_encode($jsonContent));
    }
    #[Route('/json/delete/{id}', name: 'app_teams_json_delete', methods: ['GET', 'POST'])]
    public function deleteJson(Request $request, NormalizerInterface $normalizer, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $team= $em->getRepository(Teams::class)->find($id);
        $em->remove($team);
        $em->flush();
        $jsonContent = $normalizer->normalize($team, 'json', ['groups'=>'post:read']);
        return new Response("Game deleted".json_encode($jsonContent));
    }
    #[Route('/{id}', name: 'app_join_requests_show', methods: ['GET'])]
    public function show(JoinRequests $joinRequest): Response
    {
        return $this->render('join_requests/show.html.twig', [
            'join_request' => $joinRequest,
        ]);
    }
=======
>>>>>>> Stashed changes


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