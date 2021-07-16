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
                //'empty_data' => new DateTime()
            ])
            ->add('registrationClosingDate', DateType::class, [
                'label' => 'Fin des inscriptions',
                'widget' => 'single_text',
                //'empty_data' => new Date()
            ])
            ->add('maxRegistrants', IntegerType::class, [
                'label' => 'Places'
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée'
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
            ->add('city', EntityType::class, [
                'label' => 'Ville',
                'class' => City::class,
                'choice_label' => function (City $city) {
                    return $city->getName();
                },
                'placeholder' => '- Sélectionnez une ville -',
                'mapped' => false
            ])

            //->add('city', EditCityFormType::class)
            //->add('location', EditLocationFormType::class)
            /*
            */;
            
        // Form loaded
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                //$outing = $event->getData();
                //$city = $form->get('city')->getData();
                $this->addLocationField($form, null);
            }
        );

        // Form submitted
        $builder->get('city')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $field = $event->getForm();
                $cityId = $event->getData();
                $city = $field->getData();
                $this->addLocationField($field->getParent(), $city);
            }
        );
    }

    /**
     * Adds the location field with locations related to the specified city.
     *
     * @param FormInterface $form
     * @param City|null $city
     * @return void
     */
    private function addLocationField(FormInterface $form, ?City $city): void {
        if (!$city) {
            $locations = [];
            $disabled = true;
            $required = false;
        } else {
            $locations = $city->getLocations();
            $disabled = false;
            $required = true;
        }
        $form->add('location', EntityType::class, [
            'label' => 'Lieu',
            'class' => Location::class,
            'choices' => $locations,
            'choice_label' => function (Location $location) {
                return $location->getName();
            },
            'placeholder' => '- Sélectionnez un lieu -',
            'disabled' => $disabled,
            'required' => $required
        ]);
    }


    /*

            //->add('campus')
            //->add('city')
            ->add('location', EntityType::class, [
                'label' => 'Lieu',
                'class' => Location::class,
                /*'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->join('l.city', 'c')
                        ->where('c.id = :cityId')
                        ->setParameter('cityId', 1);
                'choices' => $city ? $city->getLocations() : [],
                },

                'choices' => [],
                'choice_label' => 'name',
                'placeholder' => '--Sélectionnez une ville--'
            ]);
        //->add('street')
        //->add('postalCode')
        //->add('latitude')
        //->add('longitude')


        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();


                $city = $form->get('city')->getData();

                
                dump($form);
                dump($data);
                dump($city);

                if (!$city) {
                    return;
                }

                $form->add('location', EntityType::class, [
                    'label' => 'Lieu',
                    'class' => Location::class,
                    'choices' => $city->getLocations(),
                    'choice_label' => 'name',
                    'mapped' => false
                ]);
            }
        );*/

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
