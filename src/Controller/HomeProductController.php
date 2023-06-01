<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeProductController extends AbstractController
{
    #[Route('/accueilArticle', name: 'app_accueil_article')]
    public function index(): Response
    {
        return $this->render('home_product/homeProduct.html.twig');
    }
}
