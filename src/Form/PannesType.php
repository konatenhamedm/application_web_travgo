<?php

namespace App\Form;

use App\Entity\Modele;
use App\Entity\Pannes;
use App\Entity\Vehicules;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PannesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('datePanne')
            ->add('description')
            ->add('latitude')
            ->add('longitude')
            ->add('vehicule', EntityType::class, [
                'class' => Vehicules::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'immatriculation',

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pannes::class,
        ]);
    }
}
