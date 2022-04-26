<?php

namespace App\Controller;

use App\Entity\Teams;
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
#[Route('/teams')]
class TeamsController extends AbstractController
{

        #[Route('/listDQL', name: 'app_teams_listDql')]
    function orderByNameDQL(EntityManagerInterface $entityManager): Response
    {
       
        $team = $entityManager->getRepository(Teams::class)->orderByName();
        return $this->render('teams/index.html.twig', array("team" => $team));
    }
    
    #[Route('/', name: 'app_teams_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $teams = $entityManager
            ->getRepository(Teams::class)
            ->findAll();

        return $this->render('teams/index.html.twig', [
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
    public function show(Teams $team,EntityManagerInterface $entityManager,Request $request): Response
    {
        $playedMatchesCount = count($entityManager
        ->getRepository(Matches::class)
        ->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('team1', $entityManager->getRepository(Teams::class)->find($request->get('id'))))
                ->orWhere(Criteria::expr()->eq('team2', $entityManager->getRepository(Teams::class)->find($request->get('id')))
            )
        ));

        $wonMatchesCount = count($entityManager
        ->getRepository(Matches::class)
        ->findBy(["winnerTeam" => $entityManager->getRepository(Teams::class)->find($request->get('id'))]));

        if($playedMatchesCount !=0){
        return $this->render('teams/show.html.twig', [
            'team' => $team,
            
            'winrate' => ($wonMatchesCount *100) / ($playedMatchesCount ),
        ]);
   
    }
    else{
        return $this->render('teams/show.html.twig', [
            'team' => $team,
            
            'winrate' => 0,
        ]);

    }
}

    #[Route('/{id}/edit', name: 'app_teams_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Teams $team, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
      
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
