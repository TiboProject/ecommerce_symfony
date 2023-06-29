<?php

namespace App\Controller;

use App\Form\SelectTeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request,EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(SelectTeamType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            dd($form->getData());
            $user->setFavTeam(
                $form->get('favTeam')->getData()
            );

            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->render('profile/profile.html.twig',[
            'SelectTeam' => $form->createView(),
        ]);
    }
}
