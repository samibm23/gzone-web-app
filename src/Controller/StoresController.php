<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use \Twilio\Rest\Client;

use App\Entity\UserLikesDislikes;
use App\Entity\Games;
use App\Entity\Stores;
use App\Entity\MarketItems;
use App\Entity\Users;
use App\Form\StoresType;
use App\Form\MarketItemsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Include paginator interface
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
            $storeMap->put($store, (count($likes) - count($dislikes)));
            $storeMap->sort(function ($a, $b) {
                return $b <=> $a;
            });
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
            'sortedStores' => $storeMap,
            "user_id" => $this->getUser()->getId()
        ]);
    }
    #[Route('/json/list', name: 'app_stores_json_list', methods: ['GET'])]
    public function listJson(
        EntityManagerInterface $entityManager
    ): Response {
        $stores = $entityManager->getRepository(Stores::class)->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($stores, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response($jsonContent);
    }

    #[Route('/json/list/{id}', name: 'app_stores_json_get', methods: ['GET'])]
    public function showId(Request $request, $id, NormalizerInterface $normalizer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $store = $em->getRepository(Stores::class)->find($id);
        $jsonContent = $normalizer->normalize($store, 'json', ['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/json/new', name: 'app_stores_json_new', methods: ['GET', 'POST'])]
    public function newJson(Request $request, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
    {
        $em = $this->getDoctrine()->getManager();
        $store= new Stores();

        $store->setName($request->get('name'));
        $store->setOwner($entityManager->getRepository(Users::class)->find((int)$request->get("owner_id")));
        $em->persist($store);
        $em->flush();
        $jsonContent = $normalizer->normalize($store, 'json', ['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/json/update/{id}', name: 'app_stores_json_update', methods: ['GET', 'POST'])]
    public function updateJson(Request $request, NormalizerInterface $normalizer,$id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $store= $em->getRepository(Stores::class)->find($id);
        $store->setName($request->get('name'));

        $em->persist($store);
        $em->flush();
        $jsonContent = $normalizer->normalize($store, 'json', ['groups'=>'post:read']);
        return new Response("Information update".json_encode($jsonContent));
    }
    #[Route('/json/delete/{id}', name: 'app_stores_json_delete', methods: ['GET', 'POST'])]

        public function deleteJson(Stores $store, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($store);
        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($store, 'json', [
            'groups' => 'post:read',
        ]);        return new Response("Store deleted" . $jsonContent);
    }

    #[Route('/new', name: 'app_stores_new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $store = new Stores();
        $store->setOwner($this->getUser());
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
    #[Route('/{id}/market-items/new', name: 'app_stores_market_items_new', methods: ['GET', 'POST'])]
    public function newMarketItem(Request $request, EntityManagerInterface $entityManager,Stores $store): Response
    {
        $marketItem = new MarketItems();
        $marketItem->setStore($store);
        $marketItem->setSold(false);
        $form = $this->createForm(MarketItemsType::class, $marketItem);
        $form->handleRequest($request);
        $date = new \DateTime('now');
        $marketItem->setPostDate($date);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($marketItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_stores_show', ["id"=>$store->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_items/new.html.twig', [
            'market_item' => $marketItem,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/market-items/json/new', name: 'app_stores_market_items_json_new', methods: ['GET', 'POST'])]
    public function newMkJson(Request $request, NormalizerInterface $normalizer, EntityManagerInterface $entityManager): Response
     {
        $em = $this->getDoctrine()->getManager();
        $marketItem= new MarketItems();

        $marketItem->setTitle($request->get('title'));
        $marketItem->setDescription($request->get('description'));
        $marketItem->setSold($request->get('sold'));
        $marketItem->setStore($entityManager->getRepository(Stores::class)->find((int)$request->get("store_id")));
        $date = new \DateTime('now');
        $marketItem->setPostDate($date);
        $em->persist($marketItem);
        $em->flush();
        $jsonContent = $normalizer->normalize($marketItem, 'json', ['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/{id}/market-items/json/list', name: 'app_stores_market_items_json_list', methods: ['GET'])]
    public function listMkJson(
        EntityManagerInterface $entityManager,
        Stores $store
    ): Response {
        $marketItems = $entityManager->getRepository(MarketItems::class)->findBy([
            'store' => $store
        ]);
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($marketItems, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response($jsonContent);
    }
   

    #[Route('/{id}', name: 'app_stores_show', methods: ['GET'])]
    public function show(Stores $store, EntityManagerInterface $entityManager): Response
    {     $marketItems= $entityManager->getRepository(MarketItems::class)->findBy(['store' => $store]);
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
            'user_id' => $this->getUser()?->getId(),
            'marketItems' => $marketItems
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
