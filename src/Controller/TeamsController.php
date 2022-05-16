<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Entity\Users;
use App\Entity\Games;
use App\Entity\JoinRequests;
use App\Entity\Matches;
use App\Form\TeamsType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/teams')]
class TeamsController extends AbstractController
{

    #[Route('/json', name: 'app_teams_json_index', methods: ['GET'])]
    public function indexJson(
        EntityManagerInterface $entityManager
    ): Response {
        $teams = $entityManager->getRepository(Teams::class)->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($teams, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response($jsonContent);
    }
    #[Route('/json/new', name: 'app_teams_json_new', methods: ['GET', 'POST'])]
    public function newJson(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $team = new Teams();
        $team->setAdmin($entityManager->getRepository(Users::class)->find((int)$request->get("admin_id")));
        $team->setPhotoUrl($request->get('photo_url'));
        $team->setName($request->get('name'));
        $team->setDescription($request->get('description'));
        $team->setGame($entityManager->getRepository(Games::class)->find((int)$request->get("game_id")));
        $team->setTeamSize($request->get('team_size'));
        $team->setRequestable($request->get('requestable'));
        $team->setInvitable($request->get('invitable'));
        $date = new \DateTime('now');
        $team->setCreateDate($date);
        $entityManager->persist($team);
        $entityManager->flush();

        return new Response(json_encode("Success"));
    }

    #[Route('/json/{id}', name: 'app_teams_json_show', methods: ['GET'])]
    public function showJson(
        Teams $team
    ): Response {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($team, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response($jsonContent);
    }


    #[Route('/json/edit/{id}', name: 'app_teams_json_update', methods: ['GET', 'POST'])]
    public function updateJson(Request $request, EntityManagerInterface $entityManager, Teams $team): Response
    
    {
        if ($request->get('name') != null) $team->setName($request->get('name'));
        if ($request->get('description') != null) $team->setDescription($request->get('description'));
        if ($request->get('requestable') != null) $team->setRequestable((bool)$request->get('requestable'));
        if ($request->get('invitable') != null) $team->setInvitable((int)$$request->get('invitable'));

        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($team, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response("Information update" . $jsonContent);
    }

    #[Route('/json/delete/{id}', name: 'app_teams_json_delete', methods: ['GET', 'POST'])]
    public function deleteJson(Teams $team, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($team);
        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($team, 'json', [
            'groups' => 'post:read',
        ]);       
         return new Response("Team deleted" . $jsonContent);
    }
   #[Route('/', name: 'app_teams_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $teams = $entityManager
            ->getRepository(Teams::class)
            ->findAll();
            $teams = $paginator->paginate(
                // Doctrine Query, not results
                $teams,
                // Define the page parameter
                $request->query->getInt('page', 1),
                // Items per page
                5
            );

        return $this->render('teams/index.html.twig', [
            'userId' =>$this->getUser()->getId(),
            'teams' => $teams,
        ]);
    }

    
    #[Route('/new', name: 'app_teams_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FlashyNotifier $flashy, SluggerInterface $slugger): Response
    {
        $team = new Teams();
        
        $team->setAdmin($this->getUser());
        $form = $this->createForm(TeamsType::class, $team);
        $form->handleRequest($request);
        $date = new \DateTime('now'); 
        $team->setCreateDate($date);
       

        if ($form->isSubmitted() && $form->isValid()) {
   
    
            $entityManager->persist($team);
            $photoUrl = $form->get('photoUrl')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photoUrl) {
                $originalFilename = pathinfo($photoUrl->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoUrl->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoUrl->move(
                        $this->getParameter('team_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $team->setPhotoUrl($newFilename);
            }
            $entityManager->flush();
            $flashy->success('Team created!');
            return $this->redirectToRoute('app_teams_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('teams/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_teams_show', methods: ['GET'])]
    public function show(Teams $team,EntityManagerInterface $entityManager,Request $request,  $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $team= $em->getRepository(Teams::class)->find($id);
        
        $playedMatchesCount = count($entityManager
        
        ->getRepository(Matches::class)
        ->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('team1', $entityManager->getRepository(Teams::class)->find($request->get('id'))))
                ->orWhere(Criteria::expr()->eq('team2', $entityManager->getRepository(Teams::class)->find($request->get('id')))
            )
        ));
        $joinedUsers = count($entityManager->getRepository(JoinRequests::class)->findBy([
            "team" => $team,
            "accepted" => true,
            "tournament" => null,
        ]));

        
        $wonMatchesCount = count($entityManager
        ->getRepository(Matches::class)
        ->findBy(["winnerTeam" => $entityManager->getRepository(Teams::class)->find($request->get('id'))]));


        if($playedMatchesCount !=0){
        return $this->render('teams/show.html.twig', [
            'team' => $team,
            'requestingUserId' => $this->getUser()->getId(),
            'jr' => $entityManager->getRepository(JoinRequests::class)->findOneBy(['user'=> $this->getUser(), 'team' =>$team]),
            'winrate' => ($wonMatchesCount *100) / ($playedMatchesCount),
            'joinedUsers'=> $joinedUsers,
            
        ]);
   
    } else {
        return $this->render('teams/show.html.twig', [
            'team' => $team,
            'requestingUserId' =>$this->getUser()->getId(),
            'jr' => $entityManager->getRepository(JoinRequests::class)->findOneBy(['user'=> $this->getUser(), 'team' =>$team]),
            'winrate' => 0,
            'joinedUsers'=> $joinedUsers,
           
        ]);

    }
}
    #[Route('/{id}/accept-join-request/{jrid}', name: 'app_join_requests_accept_team', methods: ['GET', 'POST'])]
    public function acceptTeam(Request $request, EntityManagerInterface $entityManager): Response
    {
        $joinRequest=$entityManager->getRepository(JoinRequests::class)->find((int)$request->get('jrid'));
        $joinRequest->setAccepted(true);
        $entityManager->flush();

       return $this->redirectToRoute('app_teams_edit', [
            'id'=>$joinRequest->getTeam()->getId()
        ]);
    }
    #[Route('/{id}/decline-join-request/{jrid}', name: 'app_join_requests_decline_team', methods: ['GET', 'POST'])]
    public function declineTeam(Request $request, EntityManagerInterface $entityManager): Response
    {
        $joinRequest=$entityManager->getRepository(JoinRequests::class)->find((int)$request->get('jrid'));
        $joinRequest->setAccepted(false);
        $entityManager->flush();

       return $this->redirectToRoute('app_teams_edit', [
            'id'=>$joinRequest->getTeam()->getId()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_teams_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Teams $team, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $joinRequests = $entityManager
        ->getRepository(JoinRequests::class)
        ->findBy(['team'=>$team]);
        if ($this->getUser()->getId() != $team->getAdmin()->getId()) {
            return $this->redirectToRoute('app_teams_index', [], Response::HTTP_SEE_OTHER);
        }
        
        $form = $this->createForm(TeamsType::class, $team);
        $form->handleRequest($request);
        
      
        

        if ($form->isSubmitted() && $form->isValid()) {
            $photoUrl = $form->get('photoUrl')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photoUrl) {
                $originalFilename = pathinfo($photoUrl->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoUrl->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoUrl->move(
                        $this->getParameter('team_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $team->setPhotoUrl($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_teams_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('teams/edit.html.twig', [
            'team' => $team,
            'join_requests' => $joinRequests,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_teams_delete', methods: ['POST'])]
    public function delete(Request $request, Teams $team, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getId() != $team->getAdmin()->getId()) {
            return $this->redirectToRoute('app_teams_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_teams_index', [], Response::HTTP_SEE_OTHER);
    }
    

}
