<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Location;
use App\Entity\Outing;
use App\Repository\LocationRepository;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Valid;

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
                'label' => 'Date',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
            ])
            ->add('registrationClosingDate', DateType::class, [
                'label' => 'Fin des inscriptions',
                'widget' => 'single_text',
            ])
            ->add('maxRegistrants', IntegerType::class, [
                'label' => 'Places'
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (min)'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
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
                'label' => 'Ajouter',
                'required' => false,
                'mapped' => false
            ])
            ->add('newLocation', EditLocationFormType::class, [
                'label' => 'Nouveau lieu',
                //'constraints' => array(new Valid()),
                //'disabled' => !$isNewLocation,
                'mapped' => false
            ])


            ->add('location', EditLocationFormType::class, [
                'label' => 'Nouveau lieu 2',
                //'constraints' => array(new Valid())
                'required' => false,
                'setter' => function (Outing &$outing, ?Location $location, FormInterface $field): void {
                    $form = $field->getParent();

                    $isNewLocation = $form->get('isNewLocation')->getData();
                    if ($isNewLocation) {
                        $loc = $form->get('newLocation')->getData();
                        if ($loc) {
                            $loc->setCity($form->get('newLocation')->get('city')->getData());
                        }
                    } else {
                        $loc = $form->get('existingLocation')->getData();
                    }
                    //dump($loc);

                    $outing->setLocation($loc);
                    //$location = $loc;
    
                    //$form->get('location')->setData($location);
                }
            ]);


//TODO:
/*
Activer la validation uniquement si la case est cochée.
Maintenir le rechargement des lieux en fonction de la ville.
Remplir indirectement l'attribut "location" ici
*/



        //$this->addNewLocationField($builder->getForm());
        
            /* ->add('newLocation', EditLocationFormType::class, [
                'label' => 'Nouveau lieu',
                'mapped' => false
            ]) */

        // On form load
        /*$builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $outing = $event->getData();
            //    $city = $form->get('newLocation')->get('city')->getData();
                //$isNewLocation = $form->get('isNewLocation')->getData();
                $this->addExistingLocationField($form, null);
                $this->addNewLocationField($form);
            }
        );*/

        $builder->get('isNewLocation')->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $field = $event->getForm();
                $form = $field->getParent();
                $isNewLocationValue = $event->getData();
                $isNewLocation = $field->getData();


                $this->addNewLocationField($form);

                $city = $form->get('newLocation')->get('city')->getData();
                //$this->addExistingLocationField($form, null);
                
                
                $this->addExistingLocationField($form, $city);
            }
        );
/*
        // On form submitted (fill the location attribute)
        $builder->get('location')->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $field = $event->getForm();
                $form = $field->getParent();
                //$locationValue = $event->getData();
                //$location = $field->getData();
                $outing = $form->getData();


                $isNewLocation = $form->get('isNewLocation')->getData();
                //$isNewLocation = isset($outingValue['isNewLocation']) && $outingValue['isNewLocation'];
                
                if ($isNewLocation) {
                    $location = $form->get('newLocation')->getData();
                    if ($location) {
                        $location->setCity($form->get('newLocation')->get('city')->getData());
                    }
                } else {
                    $location = $form->get('existingLocation')->getData();
                }

                $form->get('location')->setData($location);
                dump($event);
                dump($field);
                dump($location);
            }
        );*/
                
/*
        $isNewLocation = $form->get('isNewLocation')->getData();
        if ($isNewLocation) {
            $location = $form->get('newLocation')->getData();
            if ($location) {
                $location->setCity($form->get('newLocation')->get('city')->getData());
            }
        } else {
            $location = $form->get('existingLocation')->getData();
        }
        $outing->setLocation($location);
*/

        // On form submitted (used for ajax request)
        $builder->get('newLocation')->get('city')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $field = $event->getForm();
                $form = $field->getParent()->getParent();
                $cityValue = $event->getData();
                $city = $field->getData();
                $this->addExistingLocationField($form, $city);
            }
        );
/* 
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $field = $form->get('newLocation')->get('city');
                //$cityValue = $event->getData();
                $city = $field->getData();
                
                $this->addExistingLocationField($form->getParent(), $city);
            }
        ); */
    }

    /**
     * Adds the existing location field with locations related to the specified city.
     *
     * @param FormInterface $form
     * @param City|null $city
     * @return void
     */
    private function addExistingLocationField(FormInterface $form, ?City $city): void {
        /* $isNewLocation = $form->get('isNewLocation')->getData();
        dump($isNewLocation); */

        if (!$city) {
            $locations = [];
            $placeholder = "Aucune ville sélectionnée";
        } else {
            $locations = $city->getLocations()->getValues();
            sort($locations);
            if (count($locations) > 0) {
                $placeholder = "Sélectionnez un lieu";
            } else {
                $placeholder = "Aucun lieu trouvé";
            }
        }
        $form->add('existingLocation', EntityType::class, [
            'label' => 'Lieu',
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
            'placeholder' => '- ' . $placeholder . ' -',
            //'disabled' => $isNewLocation,
            'mapped' => false
        ]);
    }

    private function addNewLocationField(FormInterface $form): void {
        $isNewLocation = $form->get('isNewLocation')->getData();
        dump($isNewLocation);

        /* $form->add('newLocation', EditLocationFormType::class, [
            'label' => 'Nouveau lieu',
            'constraints' => $isNewLocation ? array(new Valid()) : null,
            //'disabled' => !$isNewLocation,
            'mapped' => false
        ]); */
        /*$subform = $form->get('newLocation');
        $field = $subform->get('city');
        $options = $field->getConfig()->getOptions();
        $type = $field->getConfig()->getType()->;
        $options['disabled'] = false;
        $subform->add('city', $type, $options);*/

    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Outing::class
        ]);
    }
}
