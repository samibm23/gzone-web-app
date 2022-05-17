<?php

namespace App\Controller;

use App\Entity\MarketItemReports;
use App\Entity\PostReports;
use App\Entity\Posts;
use App\Entity\Reports;
use App\Entity\StoreReports;
use App\Entity\Stores;
use App\Entity\TournamentReports;
use App\Entity\Tournaments;
use App\Form\ReportsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


#[Route('/reports')]
class ReportsController extends AbstractController
{
    #[Route('/', name: 'app_reports_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reports = $entityManager
            ->getRepository(Reports::class)
            ->findAll();

        return $this->render('reports/index.html.twig', [
            'reports' => $reports,
        ]);
    }

    #[Route('/new/reportType/id', name: 'app_reports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $type = $request->get('reportType');
        $id = $request->get('id');
        $report = new Reports();
        $report->setReporter($this->getUser());
        $reportTournament = new TournamentReports();
        $reportStore = new StoreReports();
        $reportPost = new PostReports();
        $reportMarketItem = new MarketItemReports();
        $form = $this->createForm(ReportsType::class, $report);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($type == 1) {
                $reportTournament->setReport($report);
                $reportTournament->setTournament($entityManager->getRepository(Tournaments::class)->findOneBy(['id' => $id]));
                $entityManager->persist($reportTournament);
                $entityManager->persist($report);
                $entityManager->flush();
                return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
            } else if ($type == 2) {
                $reportStore->setReport($report);
                $reportStore->setStore($entityManager->getRepository(Stores::class)->findOneBy(['id' => $id]));
                $entityManager->persist($reportStore);
                $entityManager->persist($report);
                $entityManager->flush();
                return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
            } else if ($type == 3) {
                $reportPost->setReport($report);
                $reportPost->setPost($entityManager->getRepository(Posts::class)->findOneBy(['id' => $id]));
                $entityManager->persist($reportPost);
                $entityManager->persist($report);
                $entityManager->flush();
                return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
            } else if ($type == 4) {
                $reportMarketItem->setReport($report);
                $reportMarketItem->setMarketItem($entityManager->getRepository(MarketItems::class)->findOneBy(['id' => $id]));
                $entityManager->persist($reportMarketItem);
                $entityManager->persist($report);
                $entityManager->flush();
                return $this->redirectToRoute('app_home_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->renderForm('reports/new.html.twig', [
            'report' => $report,
            'form' => $form,
        ]);
    }

    #[Route('/json/reportType/id', name: 'app_reports_json', methods: ['GET', 'POST'])]
    public function newJSON(Request $request, EntityManagerInterface $entityManager): Response
    {
        $type = $request->get('reportType');
        $id = $request->get('id');
        $report = new Reports();
        $report->setReporter($this->getUser());
        $reportTournament = new TournamentReports();
        $reportStore = new StoreReports();
        $reportPost = new PostReports();
        $reportMarketItem = new MarketItemReports();
        $form = $this->createForm(ReportsType::class, $report);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($type == 1) {
                $reportTournament->setReport($report);
                $reportTournament->setTournament($entityManager->getRepository(Tournaments::class)->findOneBy(['id' => $id]));
                $entityManager->persist($reportTournament);
                $entityManager->persist($report);
                $entityManager->flush();
                return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
            } else if ($type == 2) {
                $reportStore->setReport($report);
                $reportStore->setStore($entityManager->getRepository(Stores::class)->findOneBy(['id' => $id]));
                $entityManager->persist($reportStore);
                $entityManager->persist($report);
                $entityManager->flush();
                return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
            } else if ($type == 3) {
                $reportPost->setReport($report);
                $reportPost->setPost($entityManager->getRepository(Posts::class)->findOneBy(['id' => $id]));
                $entityManager->persist($reportPost);
                $entityManager->persist($report);
                $entityManager->flush();
                return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
            } else if ($type == 4) {
                $reportMarketItem->setReport($report);
                $reportMarketItem->setMarketItem($entityManager->getRepository(MarketItems::class)->findOneBy(['id' => $id]));
                $entityManager->persist($reportMarketItem);
                $entityManager->persist($report);
                $entityManager->flush();
                return $this->redirectToRoute('app_home_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($report, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response($jsonContent);
    }

    #[Route('/{id}', name: 'app_reports_show', methods: ['GET'])]
    public function show(Reports $report): Response
    {
        return $this->render('reports/show.html.twig', [
            'report' => $report,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reports_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reports $report, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReportsType::class, $report);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reports_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('reports/edit.html.twig', [
            'report' => $report,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reports_delete', methods: ['POST'])]
    public function delete(Request $request, Reports $report, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $report->getId(), $request->request->get('_token'))) {

            $entityManager->remove($report);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reports_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/json/report/{id}", name= "report_show", methods= {"GET"})
     */

    public function showReport(
        Request $request,
        $id,
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $report = $em->getRepository(Reports::class)->find($id);
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($report, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response($jsonContent);
    }

    #[Route('/json/reports', name: 'reports_list', methods: ['GET'])]
    public function ListJson(
        EntityManagerInterface $entityManager
    ): Response {
        $reports = $entityManager->getRepository(Reports::class)->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($reports, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response($jsonContent);
    }

    #[Route('/json/delete/{id}', name: 'report_json_delete', methods: ['GET', 'POST'])]
    public function deleteJson(Reports $report, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($report);
        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($report, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response("User deleted" . $jsonContent);
    }
}
