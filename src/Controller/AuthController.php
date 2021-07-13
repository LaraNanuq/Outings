<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController {

    /**
     * @Route("/auth/login", name="auth_login")
     */
    public function login(): Response {
        return $this->render('auth/login.html.twig', []);
    }

    /**
     * @Route("/auth/logout", name="auth_logout")
     */
    public function logout(): Response {
        // Intercepté par système d'authentification
        return $this->redirectToRoute('auth_login');
    }

    /**
     * @Route("/auth/forget", name="auth_forget")
     */
    public function forget(): Response {
        return $this->render('auth/forget.html.twig', []);
    }
}
