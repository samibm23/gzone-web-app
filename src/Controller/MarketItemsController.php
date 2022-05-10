<?php

namespace App\Controller;

use App\Entity\MarketItems;
use App\Form\MarketItemsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
#[Route('/market-items')]
class MarketItemsController extends AbstractController
{
    #[Route('/', name: 'app_market_items_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $marketItems = $entityManager
            ->getRepository(MarketItems::class)
            ->findAll();

        return $this->render('market_items/index.html.twig', [
            'market_items' => $marketItems,
        ]);
    }

    #[Route('/new', name: 'app_market_items_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $marketItem = new MarketItems();
        $form = $this->createForm(MarketItemsType::class, $marketItem);
        $form->handleRequest($request);
        $date = new \DateTime('now'); 
        $marketItem->setPostDate($date);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($marketItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_market_items_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_items/new.html.twig', [
            'market_item' => $marketItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_items_show', methods: ['GET'])]
    public function show(MarketItems $marketItem): Response
    {
        return $this->render('market_items/show.html.twig', [
            'market_item' => $marketItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_market_items_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MarketItems $marketItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MarketItemsType::class, $marketItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stores_show', ['id'=>$marketItem->getStore()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_items/edit.html.twig', [
            'market_item' => $marketItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_items_delete', methods: ['POST'])]
    public function delete(Request $request, MarketItems $marketItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marketItem->getId(), $request->request->get('_token'))) {
            $entityManager->remove($marketItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stores_show', ['id'=>$marketItem->getStore()->getId()], Response::HTTP_SEE_OTHER);
    }
}
