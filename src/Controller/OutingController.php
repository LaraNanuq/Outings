<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\EditOutingFormType;
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
 */
class OutingController extends AbstractController {

    /**
     * @Route("/list", name = "list")
     */
    public function list(
        Request $request,
        OutingRepository $outingRepository
    ): Response {

        /*
        // Default values
        $searchFilter = new SearchOutingFilter();
        $searchFilter->setIsUserOrganizer($request->query->getBoolean('isUserOrganizer', true));
        $searchFilter->setIsUserRegistrant($request->query->getBoolean('isUserRegistrant', true));
        $searchFilter->setIsUserNotRegistrant($request->query->getBoolean('isUserNotRegistrant', true));
        // TODO: Pagination
        $searchFilter->setPage($request->query->getInt('page', 1));
        $searchFilter->setItemsPerPage($request->query->getInt('itemsPerPage', 50));
        $searchForm = $this->createForm(SearchOutingFormType::class, $searchFilter);
        */

        $searchForm = $this->createForm(SearchOutingFormType::class);
        $searchForm->handleRequest($request);
        $searchFilter = $searchForm->getData();
        $outings = $outingRepository->findWithSearchFilter($searchFilter, $this->getUser());
        return $this->renderForm('outing/list.html.twig', [
            'searchForm' => $searchForm,
            'outings' => $outings
        ]);
    }

    /**
     * @Route("/{id}", name = "detail", requirements = {"id"="\d+"})
     */
    public function detail(int $id, OutingRepository $outingRepository): Response {
        $outing = $outingRepository->find($id);
        if (!$outing) {
            throw $this->createNotFoundException("La sortie n'existe pas ou a été supprimée.");
        }
        return $this->render('outing/detail.html.twig', [
            "outing" => $outing
        ]);
    }

    /**
     * @Route("/create", name = "create")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        OutingStateRepository $outingStateRepository
    ): Response {
        $outing = new Outing();
        $form = $this->createForm(EditOutingFormType::class, $outing)
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer comme brouillon',
                'attr' => ['class' => 'btn-sm btn-primary']
            ])
            ->add('saveAndPublish', SubmitType::class, [
                'label' => 'Enregistrer et publier',
                'attr' => ['class' => 'btn-sm btn-success mx-1']
            ]);
        $form->handleRequest($request);

        // Do not validate the form on Ajax requests
        if (!$request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->getUser();
                $outing->setOrganizer($user);

                if ($form instanceof Form) {
                    if ($form->getClickedButton() === $form->get('saveAndPublish')) {
                        $outing->setState($outingStateRepository->findOneBy(['label' => 'OPEN']));
                        $successText = 'La sortie a été enregistrée et publiée.';
                    } else {
                        $outing->setState($outingStateRepository->findOneBy(['label' => 'DRAFT']));
                        $successText = 'La sortie a été enregistrée.';
                    }
                }

                $location = $outing->getLocation();
                if (!$location->getId()) {
                    $entityManager->persist($location);
                }
                $entityManager->persist($outing);
                $entityManager->flush();
                $this->addFlash('success', $successText);
                return $this->redirectToRoute('main_home');
            }
        }
        return $this->renderForm('outing/edit.html.twig', [
            'outingForm' => $form,
            'outing' => $outing
        ]);
    }

    /**
     * @Route("/edit/{id}", name = "edit", requirements = {"id"="\d+"})
     */
    public function edit(
        int $id,
        OutingRepository $outingRepository,
        OutingStateRepository $outingStateRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $outing = $outingRepository->find($id);
        if (!$outing) {
            throw $this->createNotFoundException("La sortie n'existe pas ou a été supprimée.");
        }
        $form = $this->createForm(EditOutingFormType::class, $outing)
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer comme brouillon',
                'attr' => ['class' => 'btn-sm btn-primary']
            ])
            ->add('saveAndPublish', SubmitType::class, [
                'label' => 'Enregistrer et publier',
                'attr' => ['class' => 'btn-sm btn-success mx-1']
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Supprimer',
                'attr' => ['class' => 'btn-sm btn-danger']
            ]);
        $form->handleRequest($request);

        // Do not validate the form on Ajax requests
        if (!$request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && $form->isValid() && $form instanceof Form) {
                if ($form->getClickedButton() === $form->get('delete')) {
                    $successText = 'La sortie a été supprimée.';
                    // TODO: Suppression via url, avec l'id
                    $entityManager->remove($outing);
                } else {
                    if ($form->getClickedButton() === $form->get('save')) {
                        $successText = 'La sortie a été enregistrée.';
                    } else {
                        // TODO: Publication via url, avec l'id
                        $outing->setState($outingStateRepository->findOneBy(['label' => 'OPEN']));
                        $successText = 'La sortie a été enregistrée et publiée.';
                    }

                    $location = $outing->getLocation();
                    if (!$location->getId()) {
                        $entityManager->persist($location);
                    }
                    $entityManager->persist($outing);
                }
                $entityManager->flush();
                $this->addFlash('success', $successText);
                return $this->redirectToRoute('main_home');
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
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/cancel/{id}", name = "cancel", requirements = {"id"="\d+"})
     */
    public function cancel(int $id): Response {
        return $this->render('outing/cancel.html.twig', [
        ]);
    }

    /**
     * @Route("/register/{id}", name = "register", requirements = {"id"="\d+"})
     */
    public function register(int $id): Response {
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/unregister/{id}", name = "unregister", requirements = {"id"="\d+"})
     */
    public function unregister(int $id): Response {
        return $this->redirectToRoute('outing_list');
    }

    /**
     * @Route("/delete/{id}", name = "delete", requirements = {"id"="\d+"})
     */
    public function delete(int $id): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $outing = $entityManager->getRepository('App:Outing')->find($id);

        $entityManager->remove($outing);
        $entityManager->flush();

        return $this->redirectToRoute('outing_list');
    }
}
