<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function default(): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->redirectToRoute('app_happy_hours_index');
    }
}