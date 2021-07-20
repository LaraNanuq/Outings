<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alias')
            ->add('lastName')
            ->add('firstName')
            ->add('email')
          //  ->add('plainPassword',PasswordType::class,["mapped"=>false,"required"=>false])
          //  ->add('checkPassword',PasswordType::class,["mapped"=>false,"required"=>false])
          ->add('checkPassword', RepeatedType::class, [
                  'type' => PasswordType::class,
                  'invalid_message' => 'The password fields must match.',
                  'options' => ['attr' => ['class' => 'password-field']],
                  'required' => false,"mapped"=>false,
                  'first_options'  => ['label' => 'Password'],
                  'second_options' => ['label' => 'Repeat Password'],
              ])
            ->add('phone')
            ->add('campus',EntityType::class,["class"=>Campus::class,"choice_label"=>"name","disabled"=>true])
            

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
