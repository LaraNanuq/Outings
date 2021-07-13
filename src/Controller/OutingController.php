<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/outing", name="outing_")
 */
class OutingController extends AbstractController {

    /**
     * @Route("/list", name="list")
     */
    public function list(): Response {
        return $this->render('outing/list.html.twig', []);
    }

    /**
     * @Route("/{id}", name="detail", requirements={"id"="\d+"})
     */
    public function detail(int $id): Response {
        return $this->render('outing/detail.html.twig', []);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(): Response {
        return $this->render('outing/edit.html.twig', []);
    }

    /**
     * @Route("/edit/{id}", name="edit", requirements={"id"="\d+"})
     */
    public function edit(int $id): Response {
        return $this->render('outing/edit.html.twig', []);
    }

    /**
     * @Route("/publish/{id}", name="publish", requirements={"id"="\d+"})
     */
    public function publish(int $id): Response {
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/cancel/{id}", name="cancel", requirements={"id"="\d+"})
     */
    public function cancel(int $id): Response {
        return $this->render('outing/cancel.html.twig', []);
    }

    /**
     * @Route("/register/{id}", name="register", requirements={"id"="\d+"})
     */
    public function register(int $id): Response {
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/unregister/{id}", name="unregister", requirements={"id"="\d+"})
     */
    public function unregister(int $id): Response {
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"})
     */
    public function delete(int $id): Response {
        return $this->redirectToRoute('outing_list');
    }
}
