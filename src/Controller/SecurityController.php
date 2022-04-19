<?php

namespace App\Controller;

use Prophecy\Argument\Token\TokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $hasAccess = in_array("ROLE_ADMIN", $this->getUser()->getRoles());        
            if ($hasAccess) {
                return $this->redirectToRoute('choice');
            } else {
                return $this->redirectToRoute('profile');
            }
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/denied_access", name="denied_access")
     */
    public function index(): Response
    {
        return $this->render('security/login_denied.html.twig');
    }

    /**
     * @Route("/choice", name="choice")
     */
    public function choice(AuthenticationUtils $authenticationUtils)
    {

        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/choice.html.twig', [
            'full_name' => $lastUsername,
        ]);
    }

    /**
     * @Route("/login", name="home", methods={"GET"})
     */
    public function home(AuthenticationUtils $authenticationUtils): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
}
