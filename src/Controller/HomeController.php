<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

#[Route('/home')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $httpClient = HttpClient::create(['headers' => [
            'X-RapidAPI-Host' => 'free-to-play-games-database.p.rapidapi.com',
            'X-RapidAPI-Key' => 'e5a466d3efmsh7027ddb554dd829p14c940jsne6021b74bb7b'
        ]]);
    

        $response = $httpClient->request('GET', 'https://free-to-play-games-database.p.rapidapi.com/api/game',[
            'query' => [
                'id' => '4'
            ]
        ]);

        dump($response);
        dump($response->toArray()["title"]) ;
        
        return $this->render('home/index.html.twig');
    }
}
