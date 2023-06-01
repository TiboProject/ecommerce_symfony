<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_security')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/signup', name: 'app_security')]
    public function signUp(): Response
    {
        return $this->render('security/signUp.html.twig');
    }
}
