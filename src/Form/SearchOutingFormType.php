<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\SearchOutingFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Marin Taverniers
 */
class SearchOutingFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => function (Campus $campus) {
                    return $campus->getName();
                },
                'placeholder' => '- Tous -',
                'required' => false
            ])
            ->add('name', SearchType::class, [
                'label' => 'Nom contenant',
                'required' => false
            ])
            ->add('minDate', DateType::class, [
                'label' => 'Date minimum',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('maxDate', DateType::class, [
                'label' => 'Date maximum',
                'widget' => 'single_text',
                'required' => false
            ])

            // TODO: Au moins une case cochée
            ->add('isUserOrganizer', CheckboxType::class, [
                'label' => "Sorties que j'organise",
                'required' => false
            ])
            ->add('isUserRegistrant', CheckboxType::class, [
                'label' => "Sorties auxquelles je participe",
                'required' => false
            ])
            ->add('isUserNotRegistrant', CheckboxType::class, [
                'label' => "Sorties auxquelles je ne participe pas",
                'required' => false
            ])
            ->add('isFinished', CheckboxType::class, [
                'label' => "Sorties terminées",
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => SearchOutingFilter::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    // Remove prefix for query parameters
    public function getBlockPrefix(): string {
        return '';
    }
}
