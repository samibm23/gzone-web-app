<?php

namespace App\Controller;

use App\Entity\Games;
use App\Form\GamesType;
use App\Repository\GamesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;
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
     * @Route("/tri", name="app_tri")
     */
    public function Tri(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $query = $em->createQuery(
            'SELECT a FROM App\Entity\Games a 
            ORDER BY a.name ASC'
        );

        $games = $query->getResult();

        return $this->render('games/index.html.twig',
            array('games' => $games));

    }
    /**
     * @Route("/imp", name="impr")
     */
    public function imprimer(GamesRepository $repository,EntityManagerInterface $entityManager): Response

    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $game = $entityManager
            ->getRepository(Games::class)
            ->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('games/Pdf.html.twig', [
            'games' => $game,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("list of games.pdf", [
            "Attachment" => true

        ]);
    }

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
    /**
     * @param GamesRepository $repository
     * @return Response
     * @Route ("/listDQL", name="ListDQL")
     */

    function orderByNameDQL(GamesRepository $repository): Response
    {
        $games = $repository->orderByName();
        return $this->render('games/index.html.twig', array("games" => $games));
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
            $flashy->success('Success');
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


}

/*
 #[Route('/TrierParName', name: 'TrierParName')]

public function TrierParName(Request $request): Response
{
    $repository = $this->getDoctrine()->getRepository(Games::class);
    $game = $repository->findByName();

    return $this->render('games/index.html.twig', [
        'game' => $game,
    ]);
}}
*/