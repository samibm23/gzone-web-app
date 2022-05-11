<?php
namespace App\Controller\Mobile;

use App\Entity\Badges;
use App\Repository\BadgesRepository;
use App\Repository\GamesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mobile/badge")
 */
class BadgeMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(BadgesRepository $badgeRepository): Response
    {
        $badges = $badgeRepository->findAll();

        if ($badges) {
            return new JsonResponse($badges, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, GamesRepository $gameRepository): JsonResponse
    {
        $badge = new Badges();

        return $this->manage($badge, $gameRepository,  $request, false);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, BadgesRepository $badgeRepository, GamesRepository $gameRepository): Response
    {
        $badge = $badgeRepository->find((int)$request->get("id"));

        if (!$badge) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($badge, $gameRepository, $request, true);
    }

    public function manage($badge, $gameRepository, $request, $isEdit): JsonResponse
    {   
        $game = $gameRepository->find((int)$request->get("game"));
        if (!$game) {
            return new JsonResponse("game with id " . (int)$request->get("game") . " does not exist", 203);
        }
        
        
        $badge->setUp(
            $game,
            $request->get("title")
        );
        
        

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($badge);
        $entityManager->flush();

        return new JsonResponse($badge, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, BadgesRepository $badgeRepository): JsonResponse
    {
        $badge = $badgeRepository->find((int)$request->get("id"));

        if (!$badge) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($badge);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, BadgesRepository $badgeRepository): Response
    {
        $badges = $badgeRepository->findAll();

        foreach ($badges as $badge) {
            $entityManager->remove($badge);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }
    
}
