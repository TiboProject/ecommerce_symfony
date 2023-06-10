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

        for ($i=0;$i<4;$i++)
        {
        $equipes=array('Lyon Domicile','Lyon Extérieur','Paris Domicile Mbappe','Paris Domicile');
        $image=array('https://i.goopics.net/hatckb.jpg','https://i.goopics.net/p84p1l.jpg','https://i.goopics.net/zabwn8.jpg','https://i.goopics.net/vihwm6.jpg');
        $product= new Product();
        $product->setName('Ensemble - '.$equipes[$i])
            ->setCategory('Ensemble')
            ->setPrice("90")
            ->setPicture($image[$i]);
    
            $manager->persist($product);
        }

        for ($i=0;$i<4;$i++)
        {
        $equipeM=array('Japon Domicile','Réal Madrid Domicile', 'Lyon Extérieur', 'Paris Domicile');
        $imageM=array('https://i.goopics.net/wl46vh.jpg','https://i.goopics.net/6qwabq.jpg','https://i.goopics.net/c82q6t.jpg','https://i.goopics.net/bqyffd.jpg');
        $product= new Product();
        $product->setName('Maillot - '.$equipeM[$i])
            ->setCategory('Maillot')
            ->setPrice("50")
            ->setPicture($imageM[$i]);
    
            $manager->persist($product);

        }

        for ($i=0;$i<4;$i++)
        {
        $Marque=array('Prédator X1','Prédator X2', 'Prédator X3', 'Prédator X4');
        $imageC=array('https://i.goopics.net/8l04rj.jpg','https://i.goopics.net/f1j3ce.jpg','https://i.goopics.net/prajh2.jpg','https://i.goopics.net/lcxogf.jpg');
        $product= new Product();
        $product->setName('Crampons - '.$Marque[$i])
            ->setCategory('Crampons')
            ->setPrice("30")
            ->setPicture($imageC[$i]);
    
            $manager->persist($product);

        }

        for ($i=0;$i<4;$i++)
        {
        $Marques=array('Lyon 2002','Lyon 2003', 'Lyon 2007', 'Lyon 2015');
        $imageB=array('https://i.goopics.net/t2294y.jpg','https://i.goopics.net/edy5kh.jpg','https://i.goopics.net/538jte.jpg','https://i.goopics.net/wq4nye.jpg');
        $product= new Product();
        $product->setName('Ballon - '.$Marques[$i])
            ->setCategory('Ballon')
            ->setPrice("10")
            ->setPicture($imageB[$i]);
    
            $manager->persist($product);

        }

        for ($i=0;$i<4;$i++)
        {
        $MarquesG=array('Lyon 2004','Lyon Fourrure', 'Adidas 2007', 'Adidas 2015');
        $imageG=array('https://i.goopics.net/noz4i2.jpg','https://i.goopics.net/ngnlej.jpg','https://i.goopics.net/23ujbl.jpg','https://i.goopics.net/l6121r.jpg');
        $product= new Product();
        $product->setName('Gants - '.$MarquesG[$i])
            ->setCategory('Gants')
            ->setPrice("15")
            ->setPicture($imageG[$i]);
    
            $manager->persist($product);

        }

        for ($i=0;$i<4;$i++)
        {
        $Goodies=array('Sac Paris','Mug Lyon', 'Echarpe Paris', 'Figurine Marquinhos');
        $imageGoo=array('https://i.goopics.net/t9o0a9.jpg','https://i.goopics.net/p2n2l1.jpg','https://i.goopics.net/2ijbxd.jpg','https://i.goopics.net/l9wqyh.jpg');
        $product= new Product();
        $product->setName('Goodies - '.$Goodies[$i])
            ->setCategory('Goodies')
            ->setPrice("8")
            ->setPicture($imageGoo[$i]);
    
            $manager->persist($product);

        }



        for ($i=1;$i<=5;$i++)
        {
            $user= new User();
            $user->setMail("Utilisateur$i@mail.com")
                ->setPassword($this->passwordHasher->hashPassword($user,"UtilisateurPassword$i"))
                ->setUsername("Utilisateur$i")
                ->setIsVerified(true)
                ->setRewardPoints($i);

            $manager->persist($user);
        }

        $orderLine= new OrderLine();
        $order= new Order();

        $newUser= new User();
        $newUser->setMail("Utilisateur54@mail.com")
                ->setPassword($this->passwordHasher->hashPassword($newUser,"UtilisateurPassword$54"))
                ->setUsername("Utilisateur54")
                ->setIsVerified(true)
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
