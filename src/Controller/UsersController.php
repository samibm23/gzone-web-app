<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Users;
use App\Form\SearchForm;
use App\Form\UsersType;
use App\Form\ProfileType;
use App\Form\EditUserType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/user")
 */
class UsersController extends AbstractController
{


    /**
     * @Route("/profile", name="profile", methods={"GET"})
     */
    public function profile(UsersRepository $userRepository): Response
    {


        return $this->render('users/profile.html.twig', [
            'user' => $userRepository->findOneBy(['id' => $this->getUser()->getUserIdentifier()]),
        ]);
    }

    /**
     * @Route("/profile/delete", name="delete_profile", methods={"GET"})
     */
    public function delete_profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$this->isCsrfTokenValid('delete' . $user->getUserIdentifier(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @Route("/profile/edit", name="edit_profile", methods={"GET" , "POST" })
     */
    public function edit_profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
       
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/profile_edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }



    /**
    
     * @Route("/", name="app_users_index", methods={"GET"})
     */
    public function index(UsersRepository $userRepository, Request $request): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $users = $userRepository->findSearch($data);


        return $this->render('users/index.html.twig', [
            'users' => $users, 'form' => $form->createView()
        ]);
    }



    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="app_users_show", methods={"GET"})
     */
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="app_users_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Users $user, UsersRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     *  @IsGranted("ROLE_ADMIN")
     * @Route("/delete/{id}", name="app_users_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, Users $user, UsersRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
