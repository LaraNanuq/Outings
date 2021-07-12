<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard/cities", name="dashboard_cities")
     */
    public function cities(): Response
    {
        return $this->render('dashboard/cities.html.twig', [

        ]);
    }

    /**
     * @Route("/dashboard/campuses", name="dashboard_campuses")
     */
    public function campuses(): Response
    {
        return $this->render('dashboard/campuses.html.twig', [

        ]);
    }

    /**
     * @Route("/dashboard/user/create", name="dashboard_user_create")
     */
    public function userCreate(): Response
    {
        return $this->render('dashboard/user/create.html.twig', [

        ]);
    }

    /**
     * @Route("/dashboard/user/disable", name="dashboard_user_disable")
     */
    public function userDisable(): Response
    {
        return $this->render('dashboard/user/disable.html.twig', [

        ]);
    }

    /**
     * @Route("/dashboard/outing/cancel", name="dashboard_outing_cancel")
     */
    public function outingCancel(): Response
    {
        return $this->render('dashboard/outing/cancel.html.twig', [

        ]);
    }

}
