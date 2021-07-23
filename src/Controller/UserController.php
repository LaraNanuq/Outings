<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserFormType;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name = "user_")
 * 
 * @author Daisy Greenway
 * @author Marin Taverniers
 */
class UserController extends AbstractController {
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private UserService $userService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
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
    public function edit(Request $request): Response {
        $user = $this->getUser();
        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $this->userService->setPassword($user, $plainPassword);
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', "Le profil de '{$user->getAlias()}' a été mis à jour.");
            return $this->redirectToRoute('user_detail', [
                'id' => $user->getId()
            ]);
        }
        return  $this->renderForm('user/edit.html.twig', [
            'editForm' => $form
        ]);
    }

    /* Validation exceptions */

    private function validateUserExists(?User $user): void {
        if (!$user) {
            throw $this->createNotFoundException("L'utilisateur n'existe pas ou a été supprimé.");
        }
    }
}
