<?php

namespace App\Controller;

use App\Form\EditProfileType;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

/**
 * @Route("/user", name = "user_")
 */
class UserController extends AbstractController {

    /**
     * @Route("/{id}", name = "detail", requirements = {"id"="\d+"})
     */
    public function detail(int $id,UserRepository $userRepository): Response {
        $user=$userRepository->find($id);
        if (!$user){
        throw $this->createNotFoundException("Utilisateur exsiste pas")    ;

        }
        return $this->render('user/detail.html.twig', ["user"=>$user]);

    }

    /**
     * @Route("/edit", name = "edit")
     */
    public function edit(Request $request, UserPasswordHasherInterface $hasher): Response {
        /** @var User $user */
       $user=$this->getUser();
       $form=$this->createForm(EditProfileType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $plainPassword = $form->get("checkPassword")->getData();
           if ($plainPassword) {
                $password = $hasher->hashPassword($user,$plainPassword);
                $user->setPassword($password);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('sucess', 'Profile Modifier.');

            return $this->redirectToRoute('user_detail',['id'=>$user->getId()]);
        }
   //     return $this->render('user/edit.html.twig', ["user"=>$user]);
        return  $this->renderForm('user/edit.html.twig',["form"=>$form]);











}


}
