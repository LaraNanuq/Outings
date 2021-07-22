<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\User;
use App\Form\CancelOutingFormType;
use App\Form\EditOutingFormType;
use App\Form\SearchOutingFilter;
use App\Form\SearchOutingFormType;
use App\Repository\OutingRepository;
use App\Repository\OutingStateRepository;
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
    private OutingStateRepository $outingStateRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        OutingRepository $outingRepository,
        OutingStateRepository $outingStateRepository
    ) {
        $this->entityManager = $entityManager;
        $this->outingRepository = $outingRepository;
        $this->outingStateRepository = $outingStateRepository;
    }

    /**
     * @Route("/list", name = "list")
     */
    public function list(
        Request $request,
        OutingRepository $outingRepository
    ): Response {
        $searchFilter = new SearchOutingFilter();

        // Default values
        $searchFilter->setIsUserOrganizer($request->query->getBoolean('isUserOrganizer', true));
        $searchFilter->setIsUserRegistrant($request->query->getBoolean('isUserRegistrant', true));
        $searchFilter->setIsUserNotRegistrant($request->query->getBoolean('isUserNotRegistrant', true));
        $searchForm = $this->createForm(SearchOutingFormType::class, $searchFilter);
        $searchForm->handleRequest($request);
        $outings = $outingRepository->findWithSearchFilter($searchFilter, $this->getUser());
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
        $this->validateIsUserOrganizer($this->getUser(), $outing);
        if (!$this->isOutingDraft($outing)) {
            $this->addFlash('danger', "La sortie '{$outing->getName()}' a été publiée et n'est plus modifiable.");
            return $this->redirectToRoute('outing_list');
        }
        return $this->processEditOutingForm($outing, $request);
    }

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
        if (!$request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && $form->isValid() && $form instanceof Form) {
                if (!$outing->getId()) {
                    $user = $this->getUser();
                    $outing->setOrganizer($user);
                }
                if ($form->getClickedButton() === $form->get('save')) {
                    $this->setOutingState($outing, 'DRAFT');
                    $text = "La sortie '{$outing->getName()}' a été enregistrée comme brouillon.";
                } else {
                    $this->setOutingState($outing, 'OPEN');
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
        }
        return $this->renderForm('outing/edit.html.twig', [
            'outingForm' => $form,
            'outing' => $outing
        ]);
    }

    /**
     * @Route("/publish/{id}", name = "publish", requirements = {"id"="\d+"})
     */
    public function publish(int $id): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        $this->validateIsUserOrganizer($this->getUser(), $outing);
        if (!$this->isOutingDraft($outing)) {
            $this->addFlash('primary', "La sortie '{$outing->getName()}' est déjà publiée.");
        } else {
            $this->setOutingState($outing, 'OPEN');
            $this->entityManager->persist($outing);
            $this->entityManager->flush();
            $this->addFlash('success', "La sortie '{$outing->getName()}' a été publiée.");
        }
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/cancel/{id}", name = "cancel", requirements = {"id"="\d+"})
     */
    public function cancel(int $id, Request $request): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->validateIsUserOrganizer($this->getUser(), $outing);
        }
        if (!$this->isOutingPending($outing)) {
            $this->addFlash('danger', "La sortie '{$outing->getName()}' a commencé et n'est plus annulable.");
            return $this->redirectToRoute('outing_list');
        }
        $form = $this->createForm(CancelOutingFormType::class, $outing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->setOutingState($outing, 'CANCELED');
            $this->entityManager->persist($outing);
            $this->entityManager->flush();
            $this->addFlash('success', "La sortie '{$outing->getName()}' a été annulée.");
            return $this->redirectToRoute('outing_list');
        }
        return $this->renderForm('outing/cancel.html.twig', [
            'cancelForm' => $form,
            'outing' => $outing
        ]);
    }

    /**
     * @Route("/register/{id}", name = "register", requirements = {"id"="\d+"})
     */
    public function register(int $id): Response {
        // TODO: Vérifications préalables, avec fonctions de service créées en dessous
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/unregister/{id}", name = "unregister", requirements = {"id"="\d+"})
     */
    public function unregister(int $id): Response {
        // TODO: Vérifications préalables, avec fonctions de service créées en dessous
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/delete/{id}", name = "delete", requirements = {"id"="\d+"})
     */
    public function delete(int $id): Response {
        $outing = $this->outingRepository->find($id);
        $this->validateOutingExists($outing);
        $this->validateIsUserOrganizer($this->getUser(), $outing);
        if (!$this->isOutingDraft($outing)) {
            $this->addFlash('danger', "La sortie '{$outing->getName()}' a été publiée et n'est plus supprimable.");
        } else {
            $this->entityManager->remove($outing);
            $this->entityManager->flush();
            $this->addFlash('success', "La sortie '{$outing->getName()}' a été supprimée.");
        }
        return $this->redirectToRoute('outing_list');
    }

    /* Service */

    private function setOutingState(Outing &$outing, string $label) {
        $state = $this->outingStateRepository->findOneBy(['label' => $label]);
        $outing->setState($state);
    }

    private function isOutingDraft(Outing $outing): bool {
        return (strtoupper($outing->getState()->getLabel()) === 'DRAFT');
    }

    private function isOutingPublic(Outing $outing): bool {
        return (!in_array(strtoupper($outing->getState()->getLabel()), ['DRAFT', 'ARCHIVED']));
    }

    private function isOutingPending(Outing $outing): bool {
        return (in_array(strtoupper($outing->getState()->getLabel()), ['OPEN', 'PENDING']));
    }

    /* Validation exceptions */

    private function validateOutingExists(?Outing $outing): void {
        if (!$outing) {
            throw $this->createNotFoundException("La sortie n'existe pas ou a été supprimée.");
        }
    }

    private function validateIsUserOrganizer(User $user, Outing $outing): void {
        if ($user !== $outing->getOrganizer()) {
            throw $this->createAccessDeniedException("La sortie ne peut être gérée que par son organisateur.");
        }
    }

    private function validateIsOutingPublic(Outing $outing): void {
        if (!$this->isOutingPublic($outing)) {
            throw $this->createAccessDeniedException("La sortie n'a pas encore été publiée ou a été archivée.");
        }
    }
}
