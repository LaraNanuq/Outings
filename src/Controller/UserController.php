<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController {

    /**
     * @Route("/user/{id}", name="user_detail", requirements={"id"="\d+"})
     */
    public function detail(int $id): Response {
        return $this->render('user/detail.html.twig', []);
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit")
     */
    public function edit(): Response {
        return $this->render('user/edit.html.twig', []);
    }
}
