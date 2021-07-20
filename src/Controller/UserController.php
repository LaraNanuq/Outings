<?php

namespace App\Controller;

use App\Form\EditProfileType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name = "user_")
 */
class UserController extends AbstractController {

    /**
     * @Route("/{id}", name = "detail", requirements = {"id"="\d+"})
     */
    public function detail(int $id): Response {
        return $this->render('user/detail.html.twig', []);
    }

    /**
     * @Route("/edit", name = "edit")
     */
    public function edit(UserRepository $userRepository): Response {
       $user=$this->getUser();
       $form=$this->createForm(EditProfileType::class,$user);
   //     return $this->render('user/edit.html.twig', ["user"=>$user]);
        return  $this->renderForm('user/edit.html.twig',["form"=>$form]);

        
    }

}
