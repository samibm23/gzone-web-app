<?php
namespace App\Controller\Mobile;

use App\Entity\HappyHours;
use App\Repository\HappyHoursRepository;
use App\Repository\BadgesRepository;
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
 * @Route("/mobile/happyHour")
 */
class HappyHourMobileController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function index(HappyHoursRepository $happyHourRepository): Response
    {
        $happyHours = $happyHourRepository->findAll();

        if ($happyHours) {
            return new JsonResponse($happyHours, 200);
        } else {
            return new JsonResponse([], 204);
        }
    }

    /**
     * @Route("/add", methods={"POST"})
     */
    public function add(Request $request, BadgesRepository $badgeRepository): JsonResponse
    {
        $happyHour = new HappyHours();

        return $this->manage($happyHour, $badgeRepository,  $request, false);
    }

    /**
     * @Route("/edit", methods={"POST"})
     */
    public function edit(Request $request, HappyHoursRepository $happyHourRepository, BadgesRepository $badgeRepository): Response
    {
        $happyHour = $happyHourRepository->find((int)$request->get("id"));

        if (!$happyHour) {
            return new JsonResponse(null, 404);
        }

        return $this->manage($happyHour, $badgeRepository, $request, true);
    }

    public function manage($happyHour, $badgeRepository, $request, $isEdit): JsonResponse
    {   
        $badge = $badgeRepository->find((int)$request->get("badge"));
        if (!$badge) {
            return new JsonResponse("badge with id " . (int)$request->get("badge") . " does not exist", 203);
        }
        
        
        $happyHour->setUp(
            $badge,
            DateTime::createFromFormat("d-m-Y", $request->get("startDate")),
            DateTime::createFromFormat("d-m-Y", $request->get("endDate"))
        );
        
        

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($happyHour);
        $entityManager->flush();

        return new JsonResponse($happyHour, 200);
    }

    /**
     * @Route("/delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, HappyHoursRepository $happyHourRepository): JsonResponse
    {
        $happyHour = $happyHourRepository->find((int)$request->get("id"));

        if (!$happyHour) {
            return new JsonResponse(null, 200);
        }

        $entityManager->remove($happyHour);
        $entityManager->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/deleteAll", methods={"POST"})
     */
    public function deleteAll(EntityManagerInterface $entityManager, HappyHoursRepository $happyHourRepository): Response
    {
        $happyHours = $happyHourRepository->findAll();

        foreach ($happyHours as $happyHour) {
            $entityManager->remove($happyHour);
            $entityManager->flush();
        }

        return new JsonResponse([], 200);
    }
    
}
