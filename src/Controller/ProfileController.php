<?php

namespace App\Controller;

use App\Entity\User;
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
        $form = $this->createForm(SelectTeamType::class, $user,[
            'action' => $this->generateUrl('app_profile'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $user->setFavTeam(
                $form->get('favTeam')->getData()
            );

            $entityManager->persist($user);
            $entityManager->flush();
        }
        $tableauOrderLines=[];

        if ($user->getOrders()!=null){
            $commandes = $user->getOrders();
            foreach ($commandes as $commande){
                array_push($tableauOrderLines, $commande->getOrderLines());
            }
        }
        else $commandes = null;

        if ($user->getFavTeam()!=null){
            $picture = $user->getFavTeam()->getPicture();
        }
        else $picture = "https://i.goopics.net/feu9pb.jpg";

        return $this->render('profile/profile.html.twig',[
            'SelectTeam' => $form->createView(),
            'teamPicture' => $picture,
            'commandes' => $commandes,
            'orderLines' => $tableauOrderLines
        ]);
    }
}
