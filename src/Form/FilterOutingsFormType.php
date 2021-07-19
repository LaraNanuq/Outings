<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterOutingsFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'class' => Campus::class,
                'choice_label' => function (Campus $campus) {
                    return $campus->getName();
                }
            ])
            ->add('name', SearchType::class, [
                'label' => 'Le nom de la sortie contient'
            ])
            ->add('minDate', DateType::class, [
                'label' => 'Entre',
                'widget' => 'single_text'
            ])
            ->add('maxDate', DateType::class, [
                'label' => 'et',
                'widget' => 'single_text'
            ])
            ->add('isUserOrganizer', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice"
            ])
            ->add('isUserRegistrant', CheckboxType::class, [
                'label' => "Sorties auxquelles je suis inscrit/e"
            ])
            ->add('isUserNotRegistrant', CheckboxType::class, [
                'label' => "Sorties auxquelles je ne suis pas inscrit/e"
            ])
            ->add('isFinished', CheckboxType::class, [
                'label' => "Sorties passées"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([]);
    }
}
