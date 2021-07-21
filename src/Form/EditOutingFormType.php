<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Location;
use App\Entity\Outing;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Marin Taverniers
 */
class EditOutingFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date et heure',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text'
            ])
            ->add('registrationClosingDate', DateType::class, [
                'label' => 'Fin des inscriptions',
                'widget' => 'single_text'
            ])
            ->add('maxRegistrants', IntegerType::class, [
                'label' => 'Places'
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (minutes)'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => '5', 'style' => 'resize: none;']
            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => function (Campus $campus) {
                    return $campus->getName();
                },
                'placeholder' => '- Sélectionnez un campus -'
            ])
            ->add('isNewLocation', CheckboxType::class, [
                'label' => 'Ajouter un lieu',
                'required' => false,
                'mapped' => false
            ])
            ->add('newLocation', EditLocationFormType::class, [
                'label' => 'Nouveau lieu',
                'mapped' => false
            ]);

        // On form load
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $outing = $event->getData();
                $location = $outing->getLocation();

                // Edit
                if ($location && $location instanceof Location) {
                    $city = $location->getCity();
                    $form->get('newLocation')->get('city')->setData($city);

                    // Create
                } else {
                    $city = null;
                }
                $this->addSavedLocationField($form, $city);

                // TODO: Gérer l'activation des champs au chargement
            }
        );

        // On Ajax request
        // TODO: Appeler seulement lors d'une requête Ajax
        $builder->get('newLocation')->get('city')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $field = $event->getForm();
                $form = $field->getParent()->getParent();
                $city = $field->getData();
                $this->addSavedLocationField($form, $city);
            }
        );
    }

    /**
     * Adds the saved locations field from the specified city.
     */
    private function addSavedLocationField(FormInterface $form, ?City $city): void {
        if (!$city) {
            $locations = [];
            $placeholder = "Aucune ville sélectionnée";
        } else {
            $locations = $city->getLocations()->getValues();
            if (count($locations) > 0) {
                sort($locations);
                $placeholder = "Sélectionnez un lieu";
            } else {
                $placeholder = "Aucun lieu trouvé";
            }
        }
        $form->add('location', EntityType::class, [
            'label' => 'Lieu enregistré',
            'class' => Location::class,
            'choices' => $locations,
            'choice_label' => function (Location $location) {
                return $location->getName();
            },
            'choice_attr' => function (Location $location) {
                return [
                    'street' => $location->getStreet(),
                    'latitude' => $location->getLatitude(),
                    'longitude' => $location->getLongitude()
                ];
            },
            'setter' => function (Outing &$outing, ?Location $location, FormInterface $field): void {
                $form = $field->getParent();
                $isNewLocation = $form->get('isNewLocation')->getData();
                if ($isNewLocation) {
                    $location = $form->get('newLocation')->getData();
                }
                $outing->setLocation($location);
            },
            'placeholder' => '- ' . $placeholder . ' -'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Outing::class,
            'error_mapping' => ['location' => 'newLocation']
        ]);
    }
}
