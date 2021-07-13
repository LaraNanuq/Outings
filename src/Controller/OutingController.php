<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutingController extends AbstractController {

    /**
     * @Route("/outing/list", name="outing_list")
     */
    public function list(): Response {
        return $this->render('outing/list.html.twig', []);
    }

    /**
     * @Route("/outing/create", name="outing_create")
     */
    public function create(): Response {
        return $this->render('outing/create.html.twig', []);
    }

    /**
     * @Route("/outing/{id}", name="outing_detail", requirements={"id"="\d+"})
     */
    public function detail(int $id): Response {
        return $this->render('outing/detail.html.twig', []);
    }

    /**
     * @Route("/outing/edit/{id}", name="outing_edit", requirements={"id"="\d+"})
     */
    public function edit(int $id): Response {
        return $this->render('outing/edit.html.twig', []);
    }

    /**
     * @Route("/outing/cancel/{id}", name="outing_cancel", requirements={"id"="\d+"})
     */
    public function cancel(int $id): Response {
        return $this->render('outing/cancel.html.twig', []);
    }
}
