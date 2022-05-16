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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/comments')]
class CommentsController extends AbstractController
{
     /**
 * @Route("/json", name= "app_comments_list", methods= {"GET"})
 */

    

#[Route('/json', name: 'app_comments_json_index', methods: ['GET'])]
public function indexJson(
    EntityManagerInterface $entityManager
): Response {
    $comments = $entityManager->getRepository(Comments::class)->findAll();
    $encoders = [new JsonEncoder()];
    $normalizers = [new ObjectNormalizer()];

    $serializer = new Serializer($normalizers, $encoders);
    $jsonContent = $serializer->serialize($comments, 'json', [
        'groups' => 'post:read',
    ]);

    return new Response($jsonContent);
} 

#[Route('/json/{id}', name: 'app_comments_json_show', methods: ['GET'])]
public function showJson(
    Comments $comments
): Response {
    $encoders = [new JsonEncoder()];
    $normalizers = [new ObjectNormalizer()];

    $serializer = new Serializer($normalizers, $encoders);
    $jsonContent = $serializer->serialize($comment, 'json', [
        'groups' => 'post:read',
    ]);
    return new Response($jsonContent);
}

#[Route('/json/new', name: 'app_comments_json_new', methods: ['GET', 'POST'])]
public function newJson(
    Request $request,
    EntityManagerInterface $entityManager,
): Response {
    $comment = new Comments();
    $comment->setCommenter($entityManager->getRepository(Users::class)->find((int)$request->get('commenter_id')));
    $comment->setCommentBody($request->get('comment_body'));
    $comment->setCommentDate(new \DateTime('now'));
    
    $entityManager->persist($comment);
    $entityManager->flush();

    return new Response(json_encode("Success"));
}


#[Route('/json/edit/{id}', name: 'app_comments_json_update', methods: ['GET', 'POST'])]
public function updateJson(Request $request, EntityManagerInterface $entityManager, Comments $comment): Response
{
    if ($request->get('comment_body') != null) $comment->setCommentBody($request->get('comment_body'));
    
    $entityManager->flush();

    $encoders = [new JsonEncoder()];
    $normalizers = [new ObjectNormalizer()];

    $serializer = new Serializer($normalizers, $encoders);
    $jsonContent = $serializer->serialize($comment, 'json', [
        'groups' => 'post:read',
    ]);

    return new Response("Information update" . $jsonContent);
}

#[Route('/json/delete/{id}', name: 'app_comments_json_delete', methods: ['GET', 'POST'])]
public function deleteJson(Comments $comment, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($comment);
    $entityManager->flush();

    $encoders = [new JsonEncoder()];
    $normalizers = [new ObjectNormalizer()];

    $serializer = new Serializer($normalizers, $encoders);
    $jsonContent = $serializer->serialize($comment, 'json', [
        'groups' => 'post:read',
    ]);        return new Response("Comment deleted" . $jsonContent);
}
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

        return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
