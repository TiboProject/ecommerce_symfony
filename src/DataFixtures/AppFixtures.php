<?php

namespace App\DataFixtures;

use Faker;
use DateTime;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Stock;
use App\Entity\Product;
use App\Entity\Location;
use App\Entity\OrderLine;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $ville=array('Paris','Marseille','Lyon','Lille','Clamart');
        for ($i=1;$i<=4;$i++)
        {
            $location= new Location();
            $location->setVille($ville[$i])
                ->setAdresse("$i rue Jean Pigeon")
                ->setCodePostal(94220+$i);

            $manager->persist($location);
        }

        $categories=array('Maillot','Crampons','Ballon','Goodies','Ensemble');
        $equipe=array('Paris','Marseille','Lyon','Madrid','Saint-Etienne');
        for ($i=0;$i<=4;$i++)
        {   
            $stock = new Stock();
            $product= new Product();
            $product->setName($categories[$i].'-'.$equipe[$i])
                ->setCategory($categories[$i])
                ->setPrice("$i")
                ->setPicture("https://fakeimg.pl/350x200/?text=World&font=lobster")
                ;
            $stock->setProduct($product)
                ->setNumber(10*$i);

            $manager->persist($product);
        }


        for ($i=1;$i<=5;$i++)
        {
            $user= new User();
            $user->setMail("Utilisateur$i@mail.com")
                ->setPassword($this->passwordHasher->hashPassword($user,"UtilisateurPassword$i"))
                ->setUsername("Utilisateur$i")
                ->setRewardPoints($i);

            $manager->persist($user);
        }

        $orderLine= new OrderLine();
        $order= new Order();

        $newUser= new User();
        $newUser->setMail("Utilisateur54@mail.com")
                ->setPassword($this->passwordHasher->hashPassword($newUser,"UtilisateurPassword$54"))
                ->setUsername("Utilisateur54")
                ->setRewardPoints(3612);
        $newLocation= new Location();
        $newLocation->setVille('Paris')
                ->setAdresse("9ter rue du president kennedy")
                ->setCodePostal(94220);

        $order->setUser($newUser);
        $order->setLocation($newLocation);
        $order->setStatus('en attente de livraison');
        $order->setDateOrder(new DateTime());
        $orderLine->setCommande($order);
        $newProduct= new Product();
        $newProduct->setName('Maillot neymar')
            ->setCategory('Maillot')
            ->setPicture("https://fakeimg.pl/350x200/?text=World&font=lobster")
            ->setPrice("99999");
        $orderLine->setProduct($newProduct);

        $orderLine->setQuantity(1);

        $manager->persist($order);
        $manager->persist($orderLine);
        $manager->persist($newLocation);
        $manager->persist($newProduct);
        $manager->persist($newUser);
        $manager->flush();
    }
}
