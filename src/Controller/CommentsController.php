<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Comments;
use App\Form\CommentsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/comments')]
class CommentsController extends AbstractController
{
    #[Route('/', name: 'app_comments_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $comments = $entityManager
            ->getRepository(Comments::class)
            ->findAll();

        return $this->render('comments/index.html.twig', [
            'comments' => $comments,
        ]);
    }
    
    #[Route('/new', name: 'app_comments_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user= $this->getUser();
        
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
        $date = new \DateTime('now'); 
        $comment->setCommentDate($date);
        $comment->setCommenter($user);

        if ($form->isSubmitted() && $form->isValid() && $comment->getPost()->getResolved()== false) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comments/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }
    #[Route('/new/post/{id}', name: 'app_comments_new_post', methods: ['GET', 'POST'])]
    public function newByPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user= $this->getUser();
        
        $comment = new Comments();
        $comment->setPost($entityManager->getRepository(Posts::class)->find($request->get('id')));
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
        $date = new \DateTime('now'); 
        $comment->setCommentDate($date);
        $comment->setCommenter($user);

        if ($form->isSubmitted() && $form->isValid() && $comment->getPost()->getResolved()== false) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comments/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_comments_show', methods: ['GET'])]
    public function show(Comments $comment): Response
    {
        return $this->render('comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getId() != $comment->getCommenter()->getId()) {
            return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comments_delete', methods: ['POST'])]
    public function delete(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->getId() != $comment->getCommenter()->getId()) {
            return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comments_index', [], Response::HTTP_SEE_OTHER);
    }
}
