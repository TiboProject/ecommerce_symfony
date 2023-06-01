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

class AppFixtures extends Fixture
{
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
                ->setPrice("$i");
            $stock->setProduct($product)
                ->setNumber(10*$i);

            $manager->persist($product);
        }

        for ($i=1;$i<=5;$i++)
        {
            $user= new User();
            $user->setMail("Utilisateur$i@mail.com")
                ->setPassword("UtilisateurPassword$i")
                ->setUsername("Utilisateur$i")
                ->setRewardPoints($i);

            $manager->persist($user);
        }

        $orderLine= new OrderLine();
        $order= new Order();

        $newUser= new User();
        $newUser->setMail("Utilisateur54@mail.com")
                ->setPassword("UtilisateurPassword$54")
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
