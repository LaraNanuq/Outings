<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{id}}", name="user_user")
     */
    public function user(): Response
    {
        return $this->render('user/user.html.twig', [

        ]);
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit")
     */
    public function edit(): Response
    {
        return $this->render('user/edit.html.twig', [

        ]);
    }
}
