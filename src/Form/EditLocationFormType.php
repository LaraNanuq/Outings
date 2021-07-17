<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Location;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Marin Taverniers
 */
class EditLocationFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue'
            ])
            ->add('city', EntityType::class, [
                'label' => 'Ville',
                'class' => City::class,
                'choice_label' => function (City $city) {
                    return $city->getPostalCode() . ' - ' . $city->getName();
                },
                'choice_attr' => function (City $city) {
                    return ['postalCode' => $city->getPostalCode()];
                },
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository
                        ->createQueryBuilder('c')
                        ->orderBy('c.postalCode', 'ASC');
                },
                'placeholder' => '- Sélectionnez une ville -',
                //'mapped' => false
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'scale' => 7,
                'invalid_message' => "The latitude is not a valid number."
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'scale' => 7,
                'invalid_message' => "The longitude is not a valid number."
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Location::class
        ]);
    }
}
