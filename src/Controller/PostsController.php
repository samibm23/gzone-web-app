<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Comments;
use App\Entity\Users;
use App\Entity\UserLikesDislikes;
use App\Form\PostsType;
use App\Form\CommentsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Snipe\BanBuilder\CensorWords;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


#[Route('/posts')]
class PostsController extends AbstractController
{
    /**
 * @Route("/json", name= "app_posts_list", methods= {"GET"})
 */

    

    #[Route('/json', name: 'app_posts_json_index', methods: ['GET'])]
    public function indexJson(
        EntityManagerInterface $entityManager
    ): Response {
        $posts = $entityManager->getRepository(Posts::class)->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($posts, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response($jsonContent);
    }

    #[Route('/json/new', name: 'app_posts_json_new', methods: ['GET', 'POST'])]
    public function newJson(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $post = new Posts();
        $post->setPoster($entityManager->getRepository(Users::class)->find((int)$request->get('poster_id')));
        $post->setTitle($request->get('title'));
        $post->setContent($request->get('content'));
        
        $post->setPostDate(new \DateTime('now'));
        
        

        $entityManager->persist($post);
        $entityManager->flush();

        return new Response(json_encode("Success"));
    }


    #[Route('/json/{id}', name: 'app_posts_json_show', methods: ['GET'])]
    public function showJson(
        Posts $posts
    ): Response {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($post, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response($jsonContent);
    }

    
    #[Route('/json/edit/{id}', name: 'app_posts_json_update', methods: ['GET', 'POST'])]
    public function updateJson(Request $request, EntityManagerInterface $entityManager, Posts $post): Response
    {
        if ($request->get('title') != null) $post->setTitle($request->get('title'));
        if ($request->get('content') != null) $post->setContent($request->get('content'));
        

        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($post, 'json', [
            'groups' => 'post:read',
        ]);

        return new Response("Information update" . $jsonContent);
    }

    #[Route('/json/delete/{id}', name: 'app_posts_json_delete', methods: ['GET', 'POST'])]
    public function deleteJson(Posts $post, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($post);
        $entityManager->flush();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($post, 'json', [
            'groups' => 'post:read',
        ]);        return new Response("Post deleted" . $jsonContent);
    }

    #[Route('/', name: 'app_posts_index', methods: ['GET'])]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT a FROM App\Entity\Posts a 
        ORDER BY a.title ASC'
        );

        $posts = $query->getResult();

        $posts = $paginator->paginate(
            // Doctrine Query, not results
            $posts,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('posts/index.html.twig', [
            'userId' => $this->getUser()->getId(),
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'app_posts_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $post = new Posts();
        $post->setResolved(false);
        $post->setPoster($this->getUser());
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);
        $date = new \DateTime('now');
        $post->setPostDate($date);

        if ($form->isSubmitted() && $form->isValid()) {
            $Postcontent = $form->get('content')->getData();
            $censor = new CensorWords();
            $string = $censor->censorString($Postcontent);
            $post->setContent($string['clean']);
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_posts_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('posts/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/comments/new', name: 'app_posts_comments_new', methods: ['GET', 'POST'])]
    public function newComment(
        Request $request,
        EntityManagerInterface $entityManager,
        Posts $post
    ): Response {
        $user = $this->getUser();

        $comment = new Comments();
        $comment->setPost($post);
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
        $date = new \DateTime('now');
        $comment->setCommentDate($date);
        $comment->setCommenter($user);

        if (
            $form->isSubmitted() &&
            $form->isValid() &&
            $comment->getPost()->getResolved() == false
        ) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_posts_show',
                ['id' => $post->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('comments/new.html.twig', [
            'postId' => $post->getId(),
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{pid}/comments/{cid}', name: 'app_posts_comments_show', methods: ['GET'])]
    public function showComment(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = $entityManager
            ->getRepository(Comments::class)
            ->find((int) $request->get('cid'));
        return $this->render('comments/show.html.twig', [
            'postId' => (int) $request->get('pid'),
            'comment' => $comment,
        ]);
    }

    #[Route('/{pid}/comments/{cid}/edit', name: 'app_posts_comments_edit', methods: ['GET', 'POST'])]
    public function editComment(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = $entityManager
            ->getRepository(Comments::class)
            ->find((int) $request->get('cid'));
        if ($this->getUser()->getId() != $comment->getCommenter()->getId()) {
            return $this->redirectToRoute(
                'app_comments_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_posts_comments_show',
                [
                    'pid' => (int) $request->get('pid'),
                    'cid' => (int) $request->get('cid'),
                ],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('comments/edit.html.twig', [
            'postId' => (int) $request->get('pid'),
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_posts_show', methods: ['GET'])]
    public function show(
        Posts $post,
        EntityManagerInterface $entityManager
    ): Response {
        $likes = $entityManager
            ->getRepository(UserLikesDislikes::class)
            ->findBy(['post' => $post, 'like' => 1]);
        $dislikes = $entityManager
            ->getRepository(UserLikesDislikes::class)
            ->findBy(['post' => $post, 'like' => 0]);
        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'likes' => count($likes),
            'dislikes' => count($dislikes),
            'stars' =>
                count($likes) + count($dislikes) > 0
                    ? floor(
                        (count($likes) * 5) / (count($likes) + count($dislikes))
                    )
                    : 0,
            'user_id' => $this->getUser()->getId(),
            'comments' => $entityManager
                ->getRepository(Comments::class)
                ->findBy(['post' => $post]),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_posts_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Posts $post,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser()->getId() != $post->getPoster()->getId()) {
            return $this->redirectToRoute(
                'app_posts_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_posts_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('posts/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_posts_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Posts $post,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->getUser()->getId() != $post->getPoster()->getId()) {
            return $this->redirectToRoute(
                'app_posts_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }
        if (
            $this->isCsrfTokenValid(
                'delete' . $post->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_posts_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
    /**
     * @Route("/tri", name="app_tri")
     */
    public function Tri(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT a FROM App\Entity\Comments a 
        ORDER BY a.name ASC'
        );

        $posts = $query->getResult();

        return $this->render('comments/index.html.twig', [
            'comments' => $comments,
        ]);
    }
}
