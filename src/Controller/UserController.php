<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\EditUserType;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/user", name = "user_")
 */
class UserController extends AbstractController {
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/{id}", name = "detail", requirements = {"id"="\d+"})
     */
    public function detail(int $id): Response {
        $user = $this->userRepository->find($id);
        $this->validateUserExists($user);
        return $this->render('user/detail.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/edit", name = "edit")
     */
    public function edit(Request $request, UserPasswordHasherInterface $hasher): Response {
        $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get("plainPassword")->getData();
            if ($plainPassword) {
                $password = $hasher->hashPassword($user, $plainPassword);
                $user->setPassword($password);
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', "Le profil de '{$user->getAlias()}' a été mis à jour.");
            return $this->redirectToRoute('user_detail', [
                'id' => $user->getId()
            ]);
        }
        return  $this->renderForm('user/edit.html.twig', [
            'userForm' => $form
        ]);
    }

    /* Validation exceptions */

    private function validateUserExists(?User $user): void {
        if (!$user) {
            throw $this->createNotFoundException("L'utilisateur n'existe pas ou a été supprimé.");
        }
    }
}
