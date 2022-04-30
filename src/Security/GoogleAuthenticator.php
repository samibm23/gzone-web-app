<?php

namespace App\Security;

use App\Entity\Users; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class GoogleAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;
    private $clientRegistry;
    private $entityManager;
    private $router;
    private $urlGenerator;
    public function __construct(ClientRegistry $clientRegistry, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $entityManager;
        $this->router = $router;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET');
    }
    /**
     * @return KnpU\OAuth2ClientBundle\Client\OAuth2Client
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry
            ->getClient('google');
    }
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();

        $user = $this->em->getRepository(Users::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {

            $user = new Users();
            $user->setEmail($googleUser->getEmail());
            $user->setFullName($googleUser->getName());
            $user->setUsername($googleUser->getName());
            $user->setPassword($googleUser->getName());
            $date = new \DateTime('now');
            $user->setJoinDate($date);
            $user->setBirthDate($date);
            $user->setRole("ROLE_USER");
            $bytes = random_bytes(3);
            $verificationCode = bin2hex($bytes);
            $user->SetVerificationCode($verificationCode);
            $user->setIsVerified(1);
            $this->em->persist($user);
            $this->em->flush();
        }


        return $user;
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }



    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        $activated = $token->getUser()->getIsVerified();
        $hasAccess = in_array('ROLE_ADMIN', $token->getUser()->getRoles());
        $verificationCode = $token->getUser()->getVerificationCode();
        $disabled = $token->getUser()->getDisableToken();

        if ($activated == 1) {
            if ($hasAccess) {
                return new RedirectResponse($this->urlGenerator->generate('choice'));
            } else {
                if ($disabled) {
                    return new RedirectResponse($this->urlGenerator->generate('DisabledAccount'));
                } else {
                    return new RedirectResponse($this->urlGenerator->generate('profile'));
                }
            }
        } else {
            return new RedirectResponse($this->urlGenerator->generate('denied_access'));
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response("hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh");
    }
}
