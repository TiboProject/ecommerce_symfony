<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelperInterface): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $verifyEmailHelperInterface->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getMail(),
                ['id' => $user->getId()]
            );

            ///A faire : envoyer un vrai mail, en V1 ce sera un message flash
            $this->addFlash('success', 'Confirmez votre email ici : '.$signatureComponents->getSignedUrl());

            return $this->redirectToRoute('app_security_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelperInterface, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface) : Response
    {
        $user = $userRepository->find($request->get('id'));
        if(!$user){
            $this->addFlash('danger', 'Utilisateur inconnu');
            return $this->redirectToRoute('app_security_login');
        }
        try{
            $verifyEmailHelperInterface->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getMail());
        }catch(VerifyEmailExceptionInterface $e){
            $this->addFlash('danger', $e->getReason());
            return $this->redirectToRoute('app_security_login');
        }
        $user->setIsVerified(true);
        $entityManagerInterface->flush();

        $this->addFlash('success', 'Votre email a bien été vérifié, vous pouvez maintenant vous connecter !');
        return $this->redirectToRoute('app_security_login');
    }


    #[Route('/resend/verify/email', name: 'app_resend_verify_email')]
    public function resendVerifyEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelperInterface, UserRepository $userRepository)
    {
        ///si la méthode est POST, on regenere un lien de vérification et on envoie un mail
        ///sinon on affiche le formulaire
        if($request->isMethod('POST')){
            ///on récupère l'utilisateur
            $user = $userRepository->find($request->get('id'));
            ///on regenere le lien de vérification
            $signatureComponents = $verifyEmailHelperInterface->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getMail(),
                ['id' => $user->getId()]
            );
            ///on affiche un message flash
            $this->addFlash('success', 'Un nouveau mail de vérification vous a été envoyé : '.$signatureComponents->getSignedUrl());
            ///on redirige vers la page de login
            return $this->redirectToRoute('app_security_login');
        }
        return $this->render('registration/resendVerifyEmail.html.twig');
    }
}
