<?php

namespace App\Form;

use App\Entity\Arrets;
use App\Entity\Lignes;
use App\Entity\Trajets;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrajetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle')
            ->add('ligne', EntityType::class, [
                'class' => Lignes::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'numero',

            ])
            ->add('arrets', EntityType::class, [
                'class' => Arrets::class,
                //'expanded'=>true,
                'multiple' => true,
                'choice_label' => function ($acteurs) {
                    return $acteurs->getLibelle();
                },
                'placeholder' => 'Selectionner des arrets',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trajets::class,
        ]);
    }
}
