<?php

namespace App\Form;

use App\Entity\{Utilisateur, Employe};
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          /*  ->add('employe', EntityType::class,
                [
                    'class' => Employe::class,
                    'choice_label' => 'nomComplet',
                ]
            )*/
             ->add('nom')
            ->add('prenoms')
            ->add('telephone')/*
            ->add('emails')*/
            ->add('email')
            ->add('password', RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'required'        => $options['required'],
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Répétez le mot de passe'],
                ]
            )
            ->add('roles', ChoiceType::class,
                [
                    'expanded'     => false,
                    'placeholder' => 'Choisir un role',
                    'required'     => true,
                    'attr' => ['class' => 'select2_multiple'],
                    'multiple' => true,
                    //'choices_as_values' => true,

                    'choices'  => array_flip([
                        'ROLE_USER'        => 'Utilisateur',
                        'ROLE_ADMIN'       => 'Administrateur',
                        'ROLE_SUPER_ADMIN' => 'Super Administrateur',

                    ]),
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
