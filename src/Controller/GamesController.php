<?php

namespace App\Controller;

use App\Entity\Games;
use App\Form\GamesType;
use App\Repository\GamesRepository;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/games')]
class GamesController extends AbstractController
{
    #[Route('/', name: 'app_games_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $games = $entityManager
            ->getRepository(Games::class)
            ->findAll();

        return $this->render('games/index.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/triid", name="triid")
     */

    public function Triid(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT c FROM App\Entity\Games c 
            ORDER BY c.name'
        );


        $rep = $query->getResult();

        return $this->render('games/index.html.twig',
            array('games' => $rep));

    }
   // #[Route('/List', name: 'app_games_list', methods: ['GET'])]
    //    public function ListJson(EntityManagerInterface $entityManager, NormalizerInterface $normalizer): Response
    //    {
    //        $games = $entityManager
    //            ->getRepository(Games::class)
    //            ->findAll();
    //        $jsonContent = $normalizer->normalize($games, 'json', ['groups'=>'post:read']);
    //        // return $this->render('games/index.html.twig', [
    //        //   'games' => $games,
    //        //]);
    //        return new Response(json_encode($jsonContent));
    //    }
    //
    //#[Route('/lis/{id}', name: 'app_games_lis', methods: ['GET'])]
    //    public function showId(Request $request, $id, NormalizerInterface $normalizer): Response
    //    {
    //        $em = $this->getDoctrine()->getManager();
    //        $game = $em->getRepository(Games::class)->find($id);
    //        $jsonContent = $normalizer->normalize($game, 'json', ['groups'=>'post:read']);
    //        return new Response(json_encode($jsonContent));
    //    }
    //#[Route('/newJson', name: 'app_games_newJson', methods: ['GET', 'POST'])]
    //    public function newJson(Request $request, NormalizerInterface $normalizer): Response
    //    {
    //        $em = $this->getDoctrine()->getManager();
    //        $game= new Games();
    //        $game->setName($request->get('name'));
    //        $game->setDescription($request->get('description'));
    //        $em->persist($game);
    //        $em->flush();
    //        $jsonContent = $normalizer->normalize($game, 'json', ['groups'=>'post:read']);
    //        return new Response(json_encode($jsonContent));
    //    }
    //#[Route('/updateJson/{id}', name: 'app_games_updateJson', methods: ['GET', 'POST'])]
    //    public function updateJson(Request $request, NormalizerInterface $normalizer, $id): Response
    //    {
    //        $em = $this->getDoctrine()->getManager();
    //        $game= $em->getRepository(Games::class)->find($id);
    //        $game->setName($request->get('name'));
    //        $game->setDescription($request->get('description'));
    //        $em->persist($game);
    //        $em->flush();
    //        $jsonContent = $normalizer->normalize($game, 'json', ['groups'=>'post:read']);
    //        return new Response("Information update".json_encode($jsonContent));
    //    }
    //#[Route('/deleteJson/{id}', name: 'app_games_deleteJson', methods: ['GET', 'POST'])]
    //    public function deleteJson(Request $request, NormalizerInterface $normalizer, $id): Response
    //    {
    //        $em = $this->getDoctrine()->getManager();
    //        $game= $em->getRepository(Games::class)->find($id);
    //        $em->remove($game);
    //        $em->flush();
    //        $jsonContent = $normalizer->normalize($game, 'json', ['groups'=>'post:read']);
    //        return new Response("Game deleted".json_encode($jsonContent));
    //    }


    #[Route('/stat', name: 'app_games_stat')]
    public function stat(GamesRepository $repository): Response
    {

        $games = $repository->stat();
        $data = [['rate', 'games']];
        foreach ($games as $nb) {
            $data[] = array($nb['id'], $nb['name']);
        }
        $bar = new barchart();
        $bar->getData()->setArrayToDataTable(
            $data
        );

        $bar->getOptions()->getTitleTextStyle()->setColor('#07600');
        $bar->getOptions()->getTitleTextStyle()->setFontSize(50);
        return $this->render('games/stat.html.twig', array('barchart' => $bar, 'nbs' => $games));
    }

    #[Route('/new', name: 'app_games_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FlashyNotifier $flashy): Response
    {
        $game = new Games();
        $form = $this->createForm(GamesType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ImageFile = $form->get('photo_url')->getData();
            if ($ImageFile) {

                // this is needed to safely include the file name as part of the URL

                $newFilename = md5(uniqid()) . '.' . $ImageFile->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/public/images/games';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $game->setPhotoUrl($newFilename);


            }

            $entityManager->persist($game);
            $entityManager->flush();
            //$flashy->success('Event created!', 'http://your-awesome-link.com');

            return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('games/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_games_show', methods: ['GET'])]
    public function show(Games $game): Response
    {
        return $this->render('games/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_games_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Games $game, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GamesType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ImageFile = $form->get('photo_url')->getData();
            if ($ImageFile) {
                $newFilename = md5(uniqid()) . '.' . $ImageFile->guessExtension();
                $destination = $this->getParameter('kernel.project_dir') . '/public/images/games';
                // Move the file to the directory where brochures are stored
                try {
                    $ImageFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'ImageFilename' property to store the PDF file name
                // instead of its contents
                $game->setPhotoUrl($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('app_games_index');
        }

        return $this->render('games/edit.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_games_delete', methods: ['POST'])]
    public function delete(Request $request, Games $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $game->getId(), $request->request->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
    }





    /**
     * @Route("imp", name="impr")
     */
    public function imprimer(GamesRepository $repository,EntityManagerInterface $entityManager): Response

    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $games = $entityManager
            ->getRepository(Games::class)
            ->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('games/pdf.html.twig', [
            'games' => $games,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("list of Games.pdf", [
            "Attachment" => true

        ]);
    }






}



