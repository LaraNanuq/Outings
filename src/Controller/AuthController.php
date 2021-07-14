<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/auth", name="auth_")
 */
class AuthController extends AbstractController {

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response {
        if ($this->getUser()) {
            return $this->render('auth/login.html.twig');
        }
        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('auth/login.html.twig', ['lastUsername' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response {
        // Managed by the authentication system
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }

    /**
     * @Route("/forget", name="forget")
     */
    public function forget(): Response {
        return $this->render('auth/forget.html.twig', []);
    }
}
