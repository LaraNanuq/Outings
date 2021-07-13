<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth", name="auth_")
 */
class AuthController extends AbstractController {

    /**
     * @Route("/login", name="login")
     */
    public function login(): Response {
        return $this->render('auth/login.html.twig', []);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response {
        // Managed by the authentication system
        return $this->redirectToRoute('auth_login');
    }

    /**
     * @Route("/forget", name="forget")
     */
    public function forget(): Response {
        return $this->render('auth/forget.html.twig', []);
    }
}
