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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;



#[Route('/posts')]
class PostsController extends AbstractController
{
   
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
        return $this->render('posts/index.html.twig', ['posts' => $posts]);
    }  
   

    #[Route('/List', name: 'app_posts_list', methods: ['GET'])]
    public function ListJson(
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer
    ): Response {
        $posts = $entityManager->getRepository(Posts::class)->findAll();
        $jsonContent = $normalizer->normalize($posts, 'json', [
            'groups' => 'post:read',
        ]);
        // return $this->render('games/index.html.twig', [
        //   'games' => $games,
        //]);
        return new Response(json_encode($jsonContent));
    }
   


    #[Route('/list/{id}', name: 'app_posts_list', methods: ['GET'])]
    public function showId(
        Request $request,
        $id,
        NormalizerInterface $normalizer
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id);
        $jsonContent = $normalizer->normalize($post, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/newJson', name: 'app_posts_newJson', methods: ['GET', 'POST'])]
    public function newJson(
        Request $request,
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $post = new Posts();
        $post->setPoster(
            $entityManager
                ->getRepository(Users::class)
                ->find((int) $request->get('poster_id'))
        );
        $post->setTitle($request->get('title'));
        $post->setContent($request->get('content'));
        $date = new \DateTime('now');
        $post->setPostDate($date);
        $em->persist($post);
        $em->flush();
        $jsonContent = $normalizer->normalize($post, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response(json_encode($jsonContent));
    }
    #[Route('/updateJson/{id}', name: 'app_posts_updateJson', methods: ['GET', 'POST'])]
    public function updateJson(
        Request $request,
        NormalizerInterface $normalizer,
        $id
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id);
        $post->setTitle($request->get('title'));
        $post->setContent($request->get('content'));
        $em->persist($post);
        $em->flush();
        $jsonContent = $normalizer->normalize($post, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response('Information update' . json_encode($jsonContent));
    }
    #[Route('/deleteJson/{id}', name: 'app_posts_deleteJson', methods: ['GET', 'POST'])]
    public function deleteJson(
        Request $request,
        NormalizerInterface $normalizer,
        $id
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id);
        $em->remove($post);
        $em->flush();
        $jsonContent = $normalizer->normalize($post, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response('Post deleted' . json_encode($jsonContent));
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

    #[Route('/{id}', name: 'app_posts_show', methods: ['GET'])]
    public function show(
        Posts $post,
        EntityManagerInterface $entityManager
    ): Response {
        $likes = $entityManager
            ->getRepository(UserLikesDislikes::class)
            ->findBy(['post' => $post, 'like' => 1]);
        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'likes' => $likes,
            'user_id' => $this->getUser()->getId(),
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
}
