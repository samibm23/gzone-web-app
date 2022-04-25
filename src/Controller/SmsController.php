<?php

namespace App\Controller;

use App\Form\SendType;
use Goxens\Goxens;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SmsController extends AbstractController
{
    #[Route('/sms', name: 'app_sms')]
    public function index(Request $request): Response
    {
        $apiKey = 'ROD-9RH7U93OTNP63EOIRXDUFXS25224EO1Z5NZ';
        $userUid = ' RNO9WS';
        $goxens =  new Goxens($apiKey, $userUid);
        $form= $this->createForm(SendType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()){
        $dt = $form->getData();

        $number = $dt['number'];
        $message = $dt['message'];
        $sender = $dt['sender'];
        $snd = $goxens->sendSms($apiKey,$userUid,$number,$sender,$message);
return $this->json($snd);
    }
        return $this->render('sms/sms.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
}
