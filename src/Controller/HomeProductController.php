<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeProductController extends AbstractController
{
    #[Route('/accueilArticle', name: 'app_accueil_article')]
    public function index(ProductRepository $repo): Response
    {
        $product1 = $repo->findOneBy(['name' =>'Ensemble - Lyon Domicile']);
        $product2 = $repo->findOneBy(['name' =>'Ensemble - Lyon Extérieur']); 
        $product3 = $repo->findOneBy(['name' =>'Ensemble - Paris Domicile Mbappe']);
        $product4 = $repo->findOneBy(['name' =>'Ensemble - Paris Domicile']);
        
        $productShirt1 = $repo->findOneBy(['name' =>'Maillot - Japon Domicile']);
        $productShirt2 = $repo->findOneBy(['name' =>'Maillot - Réal Madrid Domicile']);
        $productShirt3 = $repo->findOneBy(['name' =>'Maillot - Lyon Extérieur']);
        $productShirt4 = $repo->findOneBy(['name' =>'Maillot - Paris Domicile']);

        $productShoes1 = $repo->findOneBy(['name' =>'Crampons - Prédator X1']);
        $productShoes2 = $repo->findOneBy(['name' =>'Crampons - Prédator X2']);
        $productShoes3 = $repo->findOneBy(['name' =>'Crampons - Prédator X3']);
        $productShoes4 = $repo->findOneBy(['name' =>'Crampons - Prédator X4']);

        $productBall1 = $repo->findOneBy(['name' =>'Ballon - Lyon 2002']);
        $productBall2 = $repo->findOneBy(['name' =>'Ballon - Lyon 2003']);
        $productBall3 = $repo->findOneBy(['name' =>'Ballon - Lyon 2007']);
        $productBall4 = $repo->findOneBy(['name' =>'Ballon - Lyon 2015']);

        $productGloves1 = $repo->findOneBy(['name' =>'Gants - Lyon 2004']);
        $productGloves2 = $repo->findOneBy(['name' =>'Gants - Lyon Fourrure']);
        $productGloves3 = $repo->findOneBy(['name' =>'Gants - Adidas 2007']);
        $productGloves4 = $repo->findOneBy(['name' =>'Gants - Adidas 2015']);

        $productGoodies1 = $repo->findOneBy(['name' =>'Goodies - Sac Paris']);
        $productGoodies2 = $repo->findOneBy(['name' =>'Goodies - Mug Lyon']);
        $productGoodies3 = $repo->findOneBy(['name' =>'Goodies - Echarpe Paris']);
        $productGoodies4 = $repo->findOneBy(['name' =>'Goodies - Figurine Marquinhos']);

        
        return $this->render('home_product/homeProduct.html.twig',
        [
            'product' => $product1,
            'product2' => $product2,
            'product3' =>$product3,
            'product4' =>$product4,

            'productShirt' => $productShirt1,
            'productShirt2' => $productShirt2,
            'productShirt3' => $productShirt3,
            'productShirt4' => $productShirt4,

            'productShoes' => $productShoes1,
            'productShoes2' => $productShoes2,
            'productShoes3' => $productShoes3,
            'productShoes4' => $productShoes4,

            'productBall' => $productBall1,
            'productBall2' => $productBall2,
            'productBall3' => $productBall3,
            'productBall4' => $productBall4,

            'productGloves' => $productGloves1,
            'productGloves2' => $productGloves2,
            'productGloves3' => $productGloves3,
            'productGloves4' => $productGloves4,

            'productGoodies' => $productGoodies1,
            'productGoodies2' => $productGoodies2,
            'productGoodies3' => $productGoodies3,
            'productGoodies4' => $productGoodies4

        ]
    
    
    );
    }
}
