<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\SearchOutingFilter;
use App\Form\CancelOutingFormType;
use App\Form\EditOutingFormType;
use App\Form\SearchOutingFormType;
use App\Repository\OutingRepository;
use App\Service\OutingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/outing", name = "outing_")
 * 
 * @author Marin Taverniers
 * @author Ayelen Dumas
 */
class OutingController extends AbstractController {
    private EntityManagerInterface $entityManager;
    private OutingRepository $outingRepository;
    private OutingService $outingService;

    public function __construct(
        EntityManagerInterface $entityManager,
        OutingRepository $outingRepository,
        OutingService $outingService
    ) {
        $this->entityManager = $entityManager;
        $this->outingRepository = $outingRepository;
        $this->outingService = $outingService;
    }

    /**
     * @Route("/list", name = "list")
     */
    public function list(Request $request): Response {
        $searchFilter = new SearchOutingFilter();

        // Default values
        $searchFilter->setIsUserOrganizer($request->query->getBoolean('isUserOrganizer', true));
        $searchFilter->setIsUserRegistrant($request->query->getBoolean('isUserRegistrant', true));
        $searchFilter->setIsUserNotRegistrant($request->query->getBoolean('isUserNotRegistrant', true));
        $searchForm = $this->createForm(SearchOutingFormType::class, $searchFilter);
        $searchForm->handleRequest($request);
        $outings = $this->outingRepository->findWithSearchFilter($searchFilter, $this->getUser());
        return $this->renderForm('outing/list.html.twig', [
            'searchForm' => $searchForm,
            'outings' => $outings
        ]);
    }

    /**
     * @Route("/{id}", name = "detail", requirements = {"id"="\d+"})
     */
    public function detail(int $id): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        $this->validateIsOutingPublic($outing);
        return $this->render('outing/detail.html.twig', [
            "outing" => $outing
        ]);
    }

    /**
     * @Route("/create", name = "create")
     */
    public function create(Request $request): Response {
        $outing = new Outing();
        return $this->processEditOutingForm($outing, $request);
    }

    /**
     * @Route("/edit/{id}", name = "edit", requirements = {"id"="\d+"})
     */
    public function edit(int $id, Request $request): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        $this->validateIsUserOrganizer($outing);
        if (!$this->outingService->isOutingPrivate($outing)) {
            $this->addFlash('danger', "La sortie '{$outing->getName()}' a été publiée et n'est plus modifiable.");
            return $this->redirectToRoute('outing_list');
        }
        return $this->processEditOutingForm($outing, $request);
    }

    /**
     * Shows the form to create or edit an outing.
     *
     * @param Outing $outing
     * @param Request $request
     * @return Response
     */
    private function processEditOutingForm(Outing $outing, Request $request): Response {
        $form = $this->createForm(EditOutingFormType::class, $outing)
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer comme brouillon',
                'attr' => ['class' => 'btn-sm btn-primary']
            ])
            ->add('saveAndPublish', SubmitType::class, [
                'label' => 'Enregistrer et publier',
                'attr' => ['class' => 'mx-1 btn-sm btn-success']
            ]);
        $form->handleRequest($request);

        // Do not handle the submission on Ajax request
        if ((!$request->isXmlHttpRequest()) && ($form->isSubmitted()) && ($form->isValid()) && ($form instanceof Form)) {
            if (!$outing->getId()) {
                $user = $this->getUser();
                $outing->setOrganizer($user);
            }
            if ($form->getClickedButton() === $form->get('save')) {
                $this->outingService->setOutingState($outing, $this->outingService::STATE_DRAFT);
                $text = "La sortie '{$outing->getName()}' a été enregistrée comme brouillon.";
            } else {
                $this->outingService->setOutingState($outing, $this->outingService::STATE_OPEN);
                $text = "La sortie '{$outing->getName()}' a été enregistrée et publiée.";
            }
            $location = $outing->getLocation();
            if (!$location->getId()) {
                $this->entityManager->persist($location);
            }
            $this->entityManager->persist($outing);
            $this->entityManager->flush();
            $this->addFlash('success', $text);
            return $this->redirectToRoute('outing_list');
        }
        return $this->renderForm('outing/edit.html.twig', [
            'editForm' => $form,
            'outing' => $outing
        ]);
    }

    /**
     * @Route("/publish/{id}", name = "publish", requirements = {"id"="\d+"})
     */
    public function publish(int $id): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        $this->validateIsUserOrganizer($outing);
        if (!$this->outingService->isOutingPrivate($outing)) {
            $this->addFlash('primary', "La sortie '{$outing->getName()}' a déjà été publiée.");
        } else {
            $this->outingService->setOutingState($outing, $this->outingService::STATE_OPEN);
            $this->entityManager->persist($outing);
            $this->entityManager->flush();
            $this->addFlash('success', "La sortie '{$outing->getName()}' a été publiée.");
        }
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/delete/{id}", name = "delete", requirements = {"id"="\d+"})
     */
    public function delete(int $id): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        $this->validateIsUserOrganizer($outing);
        if (!$this->outingService->isOutingPrivate($outing)) {
            $this->addFlash('danger', "La sortie '{$outing->getName()}' a été publiée et n'est plus supprimable.");
        } else {
            $this->entityManager->remove($outing);
            $this->entityManager->flush();
            $this->addFlash('success', "La sortie '{$outing->getName()}' a été supprimée.");
        }
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/cancel/{id}", name = "cancel", requirements = {"id"="\d+"})
     */
    public function cancel(int $id, Request $request): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        $this->validateIsOutingPublic($outing);
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->validateIsUserOrganizer($outing);
        }
        if ($this->outingService->isOutingCanceled($outing)) {
            $this->addFlash('primary', "La sortie '{$outing->getName()}' a déjà été annulée.");
        } else if (!$this->outingService->isOutingUpcoming($outing)) {
            $this->addFlash('danger', "La sortie '{$outing->getName()}' n'est plus annulable.");
        } else {
            $form = $this->createForm(CancelOutingFormType::class, $outing);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->outingService->setOutingState($outing, $this->outingService::STATE_CANCELED);
                $this->entityManager->persist($outing);
                $this->entityManager->flush();
                $this->addFlash('success', "La sortie '{$outing->getName()}' a été annulée.");
            } else {
                return $this->renderForm('outing/cancel.html.twig', [
                    'cancelForm' => $form,
                    'outing' => $outing
                ]);
            }
        }
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/register/{id}", name = "register", requirements = {"id"="\d+"})
     */
    public function register(int $id): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        $this->validateIsOutingPublic($outing);
        $user = $this->getUser();
        if (!$this->outingService->isOutingOpenForRegistration($outing)) {
            $this->addFlash('danger', "La sortie '{$outing->getName()}' n'est plus ouverte aux inscriptions.");
        } else if (in_array($user, $outing->getRegistrants()->getValues())) {
            $this->addFlash('primary', "Vous êtes déjà inscrit(e) à la sortie '{$outing->getName()}'.");
        } else {
            $outing->addRegistrant($user);
            $this->entityManager->persist($outing);
            $this->entityManager->flush();
            $this->addFlash('success', "Vous avez été inscrit(e) à la sortie '{$outing->getName()}'.");
        }
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/unregister/{id}", name = "unregister", requirements = {"id"="\d+"})
     */
    public function unregister(int $id): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        $this->validateIsOutingPublic($outing);
        $user = $this->getUser();
        if (!$this->outingService->isOutingUpcoming($outing)) {
            $this->addFlash('danger', "La sortie '{$outing->getName()}' n'est plus ouverte aux désinscriptions.");
        } else if (!in_array($user, $outing->getRegistrants()->getValues())) {
            $this->addFlash('danger', "Vous n'êtes pas inscrit(e) à la sortie '{$outing->getName()}'.");
        } else {
            $outing->removeRegistrant($user);
            $this->entityManager->persist($outing);
            $this->entityManager->flush();
            $this->addFlash('success', "Vous avez été désinscrit(e) de la sortie '{$outing->getName()}'.");
        }
        return $this->redirectToRoute('outing_list');
    }

    /* Validation exceptions */

    public function validateOutingExists(?Outing $outing): void {
        if (!$outing) {
            throw $this->createNotFoundException("La sortie n'existe pas ou a été supprimée.");
        }
    }

    public function validateIsUserOrganizer(Outing $outing): void {
        if ($this->getUser() !== $outing->getOrganizer()) {
            throw $this->createAccessDeniedException("La sortie ne peut être gérée que par son organisateur.");
        }
    }

    public function validateIsOutingPublic(Outing $outing): void {
        if (!$this->outingService->isOutingPublic($outing)) {
            throw $this->createAccessDeniedException("La sortie n'a pas encore été publiée ou a été archivée.");
        }
    }
}
