<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController {

    /**
     * @Route("/{id}", name="detail", requirements={"id"="\d+"})
     */
    public function detail(int $id): Response {
        return $this->render('user/detail.html.twig', []);
    }

    /**
     * @Route("/edit", name="edit")
     */
    public function edit(): Response {
        return $this->render('user/edit.html.twig', []);
    }
}
