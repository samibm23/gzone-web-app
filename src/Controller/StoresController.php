<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use \Twilio\Rest\Client;

use App\Entity\UserLikesDislikes;
use App\Entity\Stores;
use App\Entity\MarketItems;
use App\Entity\Users;
use App\Form\StoresType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Include paginator interface
use Knp\Component\Pager\PaginatorInterface;
;

#[Route('/stores')]
class StoresController extends AbstractController
  {
    private $twilio;
public function __construct(Client $twilio) {
   $this->twilio = $twilio;
  
 }
    #[Route('/', name: 'app_stores_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,PaginatorInterface $paginator, Request $request): Response
    {
        $storeMap = new \Ds\Map();
        $stores = $entityManager
            ->getRepository(Stores::class)
            ->findAll();
        foreach ($stores as $store) {
            $likes = $entityManager
                ->getRepository(UserLikesDislikes::class)
                ->findBy(['store' => $store, 'like' => 1]);
            $dislikes = $entityManager
                ->getRepository(UserLikesDislikes::class)
                ->findBy(['store' => $store, 'like' => 0]);
            $storeMap->put($store, (count($likes) + count($dislikes) == 0) ? 0 : (count($likes) - count($dislikes)) / (count($likes) + count($dislikes)));
            $storeMap->reverse();
            $storeMap = $storeMap->slice(0, 3);
        }
        // Paginate the results of the query
        $stores = $paginator->paginate(
        // Doctrine Query, not results
            $stores,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            3
        );

        return $this->render('stores/index.html.twig', [
            'stores' => $stores,
            'sortedStores' => $storeMap
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
        $twilio = $this->get('twilio.client');



        foreach($entityManager->getRepository(Users::class)->findAll() as $user) {
            $twilio->messages->create("+216" .
            $user->getPhoneNumber(), // Text any number
            array(
                'from' => '19803242866', // From a Twilio number in your account
                'body' => "Bonjour , un nouveau store a été crée "
            )
        );
        }

        return new Response("Sent messages ");
    }

    #[Route('/{id}', name: 'app_stores_show', methods: ['GET'])]
    public function show(Stores $store, EntityManagerInterface $entityManager): Response
    {
        $likes = $entityManager
            ->getRepository(UserLikesDislikes::class)
            ->findBy(['store' => $store, 'like' => 1]);
        $dislikes = $entityManager
            ->getRepository(UserLikesDislikes::class)
            ->findBy(['store' => $store, 'like' => 0]);
        return $this->render('stores/show.html.twig', [
            'store' => $store,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'user_id' => $this->getUser()->getId()
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
