<?php

namespace App\Form;

use App\Entity\Arrets;
use App\Entity\Modele;
use App\Entity\Typedestinations;
use App\Entity\TypeVehicule;
use App\Entity\Zones;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArretsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle')
            ->add('latitude')
            ->add('longitude')
            ->add('zone', EntityType::class, [
                'class' => Zones::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'libelle',

            ])
            ->add('typedestinations', EntityType::class, [
                'class' => Typedestinations::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'libelle',

            ])
           /* ->add('trajets')*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Arrets::class,
        ]);
    }
}
