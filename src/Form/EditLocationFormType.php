<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditLocationFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue'
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude'
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude'
            ])
            ->add('city', EntityType::class, [
                'label' => 'Ville',
                'class' => City::class,
                'choice_label' => function (City $city) {
                    return $city->getPostalCode() . ' - ' . $city->getName();
                },
                'placeholder' => '- Sélectionnez une ville -'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
