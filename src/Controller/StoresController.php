<?php

namespace App\Controller;

use App\Entity\Stores;
use App\Form\StoresType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Include paginator interface
use Knp\Component\Pager\PaginatorInterface;
#[Route('/stores')]
class StoresController extends AbstractController
{
    #[Route('/', name: 'app_stores_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,PaginatorInterface $paginator, Request $request): Response
    {
        $stores = $entityManager
            ->getRepository(Stores::class)
            ->findAll();
        // Paginate the results of the query
        $stores = $paginator->paginate(
        // Doctrine Query, not results
            $stores,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );

        return $this->render('stores/index.html.twig', [
            'stores' => $stores,
        ]);
    }

    #[Route('/new', name: 'app_stores_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $store = new Stores();
        $form = $this->createForm(StoresType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($store);
            $entityManager->flush();

            return $this->redirectToRoute('app_stores_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stores/new.html.twig', [
            'store' => $store,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stores_show', methods: ['GET'])]
    public function show(Stores $store): Response
    {
        return $this->render('stores/show.html.twig', [
            'store' => $store,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stores_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stores $store, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StoresType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stores_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stores/edit.html.twig', [
            'store' => $store,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stores_delete', methods: ['POST'])]
    public function delete(Request $request, Stores $store, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$store->getId(), $request->request->get('_token'))) {
            $entityManager->remove($store);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stores_index', [], Response::HTTP_SEE_OTHER);
    }
}
