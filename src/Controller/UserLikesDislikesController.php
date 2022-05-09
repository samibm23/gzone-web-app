<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Posts;
use App\Entity\Comments;
use App\Entity\Stores;
use App\Entity\UserLikesDislikes;
use App\Form\UserLikesDislikesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user-likes-dislikes')]
class UserLikesDislikesController extends AbstractController
{
    #[Route('/', name: 'app_user_likes_dislikes_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $userLikesDislikes = $entityManager
            ->getRepository(UserLikesDislikes::class)
            ->findAll();

        return $this->render('user_likes_dislikes/index.html.twig', [
            'user_likes_dislikes' => $userLikesDislikes,
        ]);
    }

    #[Route('/p/{user_id}/{like}/{post_id}', name: 'app_user_post_likes_dislikes_new', methods: ['GET', 'POST'])]
    public function newPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userLikesDislike = new UserLikesDislikes();
        $userLikesDislike->setUser($entityManager->getRepository(Users::class)->find($request->get('user_id')));
        $userLikesDislike->setLike((boolean)$request->get('like'));
        $userLikesDislike->setPost($entityManager->getRepository(Posts::class)->find($request->get('post_id')));

        if (($oldUserLikesDislike = $entityManager->getRepository(UserLikesDislikes::class)->findOneBy([
            'user' => $userLikesDislike->getUser(),
            'post' => $userLikesDislike->getPost(),
        ])) != null) {
            if ($oldUserLikesDislike->getLike() == $userLikesDislike->getLike()) {
                $entityManager->remove($oldUserLikesDislike);
                $entityManager->flush();
            } else {
                $oldUserLikesDislike->setLike($userLikesDislike->getLike());
                $entityManager->flush();
            }
        } else {
            $entityManager->persist($userLikesDislike);
            $entityManager->flush();
        }
        return $this->redirectToRoute("app_posts_show", ['id' => $request->get('post_id')]);
    }

    #[Route('/s/{user_id}/{like}/{store_id}', name: 'app_user_store_likes_dislikes_new', methods: ['GET', 'POST'])]
    public function newStore(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userLikesDislike = new UserLikesDislikes();
        $userLikesDislike->setUser($entityManager->getRepository(Users::class)->find($request->get('user_id')));
        $userLikesDislike->setLike((boolean)$request->get('like'));
        $userLikesDislike->setStore($entityManager->getRepository(Stores::class)->find($request->get('store_id')));

        if (($oldUserLikesDislike = $entityManager->getRepository(UserLikesDislikes::class)->findOneBy([
                'user' => $userLikesDislike->getUser(),
                'store' => $userLikesDislike->getStore(),
            ])) != null) {
            if ($oldUserLikesDislike->getLike() == $userLikesDislike->getLike()) {
                $entityManager->remove($oldUserLikesDislike);
                $entityManager->flush();
            } else {
                $oldUserLikesDislike->setLike($userLikesDislike->getLike());
                $entityManager->flush();
            }
        } else {
            $entityManager->persist($userLikesDislike);
            $entityManager->flush();
        }
        return $this->redirectToRoute("app_stores_show", ['id' => $request->get('store_id')]);
    }

    #[Route('/{id}', name: 'app_user_likes_dislikes_show', methods: ['GET'])]
    public function show(UserLikesDislikes $userLikesDislike): Response
    {
        return $this->render('user_likes_dislikes/show.html.twig', [
            'user_likes_dislike' => $userLikesDislike,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_likes_dislikes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserLikesDislikes $userLikesDislike, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserLikesDislikesType::class, $userLikesDislike);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_likes_dislikes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_likes_dislikes/edit.html.twig', [
            'user_likes_dislike' => $userLikesDislike,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_likes_dislikes_delete', methods: ['POST'])]
    public function delete(Request $request, UserLikesDislikes $userLikesDislike, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userLikesDislike->getId(), $request->request->get('_token'))) {
            $entityManager->remove($userLikesDislike);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_likes_dislikes_index', [], Response::HTTP_SEE_OTHER);
    }
}
