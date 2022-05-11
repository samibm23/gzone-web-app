<?php

namespace App\Controller;

use App\Entity\Users;

use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class RegistrationController extends AbstractController
{
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $date = new \DateTime('now'); 
        $user->setJoinDate($date);
        $user->setRole("ROLE_USER");

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
           
            $bytes = random_bytes(3);
            $verificationCode = bin2hex($bytes);
            $user->SetVerificationCode($verificationCode);
            $user->setIsVerified(1);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email


            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('appgzone@gmail.com', 'Gzone App'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );


            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/json/register', name: 'user_register', methods: ['GET', 'POST'])]
    public function newJson(
        Request $request,
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer
    ): Response {
        $em = $this->getDoctrine()->getManager();
        $user = new Users();
        $user->setUsername($request->get('username'));
        $user->setFullName($request->get('full_name'));
        $user->setEmail($request->get('email'));
        $user->setPassword($request->get('password'));
        $user->setPhoneNumber($request->get('phone_number'));
        $user->setBio($request->get('bio'));
        $user->setPhotoUrl($request->get('photo_url'));
        $user->setBirthDate(new \DateTime('now'));
        $user->setJoinDate(new \DateTime('now'));
        $user->setActivationToken(null);
        $user->setIsVerified(true);
        $user->setDisableToken(null);
        $user->setInvitable(true);
        $user->setRole("ROLE_USER");
        $em->persist($user);
        $em->flush();
        $jsonContent = $normalizer->normalize($user, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/json/list', name: 'user_list', methods: ['GET'])]
    public function ListJson(
        EntityManagerInterface $entityManager
    ): Response {
        $users = $entityManager->getRepository(Users::class)->findAll();
	  $encoders = [new JsonEncoder()];
	  $normalizers = [new ObjectNormalizer()];

	  $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($users, 'json', [
            'groups' => 'post:read',
        ]);
        return new Response($jsonContent);
    }
}
