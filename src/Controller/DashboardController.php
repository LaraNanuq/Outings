<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard", name="dashboard_")
 */
class DashboardController extends AbstractController {

    /**
     * @Route("/user/list", name="user_list")
     */
    public function userList(): Response {
        return $this->render('dashboard/user/list.html.twig', []);
    }

    /**
     * @Route("/user/create", name="user_create")
     */
    public function userCreate(): Response {
        return $this->render('dashboard/user/create.html.twig', []);
    }

    /**
     * @Route("/user/disable/{id}", name="user_disable", requirements={"id"="\d+"})
     */
    public function userDisable(int $id): Response {
        return $this->redirectToRoute('dashboard_user_list');
    }

    /**
     * @Route("/user/delete/{id}", name="user_delete", requirements={"id"="\d+"})
     */
    public function userDelete(int $id): Response {
        return $this->redirectToRoute('dashboard_user_list');
    }

    /**
     * @Route("/campus/list", name="campus_list")
     */
    public function campusList(): Response {
        return $this->render('dashboard/campus/list.html.twig', []);
    }

    /**
     * @Route("/city/list", name="city_list")
     */
    public function cityList(): Response {
        return $this->render('dashboard/city/list.html.twig', []);
    }
}
