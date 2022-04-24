<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Snipe\BanBuilder\CensorWords;


#[Route('/posts')]
class PostsController extends AbstractController
{
    #[Route('/', name: 'app_posts_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager
            ->getRepository(Posts::class)
            ->findAll();

        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'app_posts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Posts();
        $post->setPoster($this->getUser());
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);
        $date = new \DateTime('now'); 
        $post->setPostDate($date);


        if ($form->isSubmitted() && $form->isValid()) {
            $Postcontent =$form->get('content')->getData();
            $censor = new CensorWords;
            $string = $censor->censorString($Postcontent);
            $post->setContent($string['clean']);
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('posts/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_posts_show', methods: ['GET'])]
    public function show(Posts $post): Response
    {
        return $this->render('posts/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_posts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Posts $post, EntityManagerInterface $entityManager): Response

    {
        if ($this->getUser()->getId() != $post->getPoster()->getId()) {
            return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('posts/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_posts_delete', methods: ['POST'])]
    public function delete(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getId() != $post->getPoster()->getId()) {
            return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
    }
}
