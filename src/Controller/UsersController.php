<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Users;
use App\Repository\UserGamePreferencesRepository;
use App\Form\SearchFormType;
use App\Form\UsersType;
use App\Form\ProfileType;
use App\Form\PasswordProfileType;
use App\Form\EditUserType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/user")
 */
class UsersController extends AbstractController
{


    /**
     * @Route("/profile", name="profile", methods={"GET"})
     */
    public function profile(UsersRepository $userRepository, UserGamePreferencesRepository $userGamePreferencesRepository): Response
    {
        $idcurrentuser=$this->getUser()->getUserIdentifier();
        $em = $this->getDoctrine()->getManager();


        $query = $em->createQuery('
        SELECT a FROM App\Entity\UserGamePreferences a
        WHERE a.user = :id 
    ')
    ->setParameter('id', $idcurrentuser);

        $currentuser = $query->getResult();

        return $this->render('users/profile.html.twig', [
            'user' => $userRepository->findOneBy(['id' => $this->getUser()->getUserIdentifier()]),
            'favgames' => $userGamePreferencesRepository->findBy($currentuser),
        ]);
    }

    /**
     * @Route("/profile/delete", name="delete_profile", methods={"GET", "POST"})
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
     * @Route("/profile/editPassword", name="edit_profile_password", methods={"GET", "POST"})
     */
    public function edituserpassword(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/profile_edit_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
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
    public function index(UsersRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();


        $query = $em->createQuery(
            'SELECT a FROM App\Entity\Users a 
        ORDER BY a.username ASC'
        );

        $users = $query->getResult();
        $users = $paginator->paginate(
            // Doctrine Query, not results
            $users,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            3
        );

        return $this->render(
            'users/index.html.twig',
            array('users' => $users)
        );
    }

    /**
     * @Route("/DisabledAccount", name="DisabledAccount")
     */
    public function DisabledAccount(): Response
    {
        return $this->render('users/DisabledAccount.html.twig');
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
        $form = $this->createForm(UsersType::class, $user);
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
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ActivateAccountWithCode", name="ActivateAccountWithCode", methods={"GET","POST"})
     */
    public function ActivateAccountWithCode(EntityManagerInterface $entityManager): Response
    {
        $error = null;
        if ($request->isMethod('POST')) {
            $code = $request->request->get('verificationcode');
            $codeUser = $this->getUser()->getVerificationCode();
            if ($code == $codeUser) {
                $user = $this->getUser();
                $user->setVerificationCode(null);
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute("profile");
            } else {
                $error = "Please Verify your Code";
                return $this->render('security/ActivateAccountWithCode.html.twig', [
                    'error' => $error,
                ]);
            }
        }
        return $this->render('security/ActivateAccountWithCode.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @Route("/disable_user/{id}", name="disable_user", methods={"GET", "POST"})
     */
    public function disable_user(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $user->setDisableToken("1");
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/enable_user/{id}", name="enable_user", methods={"GET", "POST"})
     */
    public function enable_user(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $user->setDisableToken(null);
        $entityManager->persist($user);
        $entityManager->flush();
        //$link = $request->headers->get("referer");
        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }
}
