<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ProfileEditionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileEditionController extends AbstractController
{
    #[Route('/profile/edition', name: 'app_profile_edition')]
    public function edition(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileEditionFormType::class, $user,[
            'action' => $this->generateUrl('app_profile_edition'),
            'method' => 'POST',
        ]);
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

            return $this->redirectToRoute('app_landing');
        }

        if ($user->getFavTeam()!=null){
            $picture = $user->getFavTeam()->getPicture();
        }
        else $picture = "https://i.goopics.net/feu9pb.jpg";

        return $this->render('profile_edition/profileEdition.html.twig', [
            'editProfileForm' => $form->createView(),
            'teamPicture' => $picture,
        ]);
    }
    

    public function index(): Response
    {
        return $this->render('profile_edition/ProfileEdition.html.twig', [
            'controller_name' => 'ProfileEditionController',
        ]);
    }
}
