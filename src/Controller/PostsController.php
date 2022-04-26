<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Users;
use App\Entity\UserLikesDislikes;
use App\Form\PostsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Snipe\BanBuilder\CensorWords;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/posts')]
class PostsController extends AbstractController
{
    #[Route('/', name: 'app_posts_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,Request $request, PaginatorInterface $paginator): Response
    {
        $posts = $entityManager
            ->getRepository(Posts::class)
            ->findAll();


            $posts = $paginator->paginate(
                // Doctrine Query, not results
                $posts,
                // Define the page parameter
                $request->query->getInt('page', 1),
                // Items per page
                5
            );
        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
            'this' => $this,
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
    public function show(Posts $post, EntityManagerInterface $entityManager): Response
    {
        $likes = $entityManager->getRepository(UserLikesDislikes::class)->findAll(['post_id' => $post->getId(), 'like' => 1]);
        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'likes' => $likes,
        ]);
    }

    #[Route('/like/{id}', name: 'app_posts_like', methods: ['GET'])]
    public function like(Request $req, Posts $post, EntityManagerInterface $entityManager): Response
    {
        $userLike = $entityManager->getRepository(UserLikesDislikes::class)->findOneBy(["post" => $post, "user" => $this->getUser()]);
        if ($userLike != null)
        {
            if ($userLike->getLike()) {
                $entityManager->remove($userLike);
            } else {
                $userLike->setLike(true);
                $entityManager->flush();
            }
        }
        else
        {
            $userLike = new UserLikesDislikes();
            $userLike->setUser($this->getUser());
            $userLike->setPost($post);
            $userLike->setLike(true);
            $entityManager->persist($userLike);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_posts_show', ["id" => $req->get('id')]);
    }

    #[Route('/dislike/{id}', name: 'app_posts_dislike', methods: ['GET'])]
    public function dislike(Request $req, Posts $post, EntityManagerInterface $entityManager): Response
    {
        $userLike = $entityManager->getRepository(UserLikesDislikes::class)->findOneBy(["post" => $post, "user" => $this->getUser()]);
        if ($userLike != null)
        {
            if ($userLike->getLike() == false) {
                $entityManager->remove($userLike);
            } else {
                $userLike->setLike(false);
                $entityManager->flush();
            }
        }
        else
        {
            $userLike = new UserLikesDislikes();
            $userLike->setUser($this->getUser());
            $userLike->setPost($post);
            $userLike->setLike(false);
            $entityManager->persist($userLike);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_posts_show', ["id" => $req->get('id')]);
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
