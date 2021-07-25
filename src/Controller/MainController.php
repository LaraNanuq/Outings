<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name = "main_")
 *
 * @author Marin Taverniers
 */
class MainController extends AbstractController {

    /**
     * @Route("/", name = "home")
     */
    public function home(): Response {
        return $this->redirectToRoute('outing_list');
    }
}
