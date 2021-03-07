<?php

namespace App\Form;

use App\Entity\Chauffeurs;
use App\Entity\Libelle;
use App\Entity\Lignes;
use App\Entity\Vehicules;
use App\Entity\Voyages;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoyagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateVoyage', DateType::Class, [
                "label" => "Date du voyage'",
                "required" => false,
                "widget" => 'single_text',
                "input_format" => 'Y-m-d',
                "by_reference" => true,
                "empty_data" => ''
            ])
            ->add('heureDepart',TimeType::class,[
                'widget'=>'single_text',
                ])

            ->add('chauffeurs', EntityType::class, [
                'class' => Chauffeurs::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'matricule',

            ])
            ->add('vehicule', EntityType::class, [
                'class' => Vehicules::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'immatriculation',

            ])
            ->add('libelle', EntityType::class, [
                'class' => Libelle::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'ASC');
                },
                'choice_label' => 'libelle',

            ])
            ->add('lignes', EntityType::class, [
                'class' => Lignes::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'numero',

            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
           // dd($data->getId());
            if ($data->getId() !== null) {

            $form->add('heureArrivee',TimeType::class,[
                'widget'=>'single_text',
            ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Voyages::class,
        ]);
    }
}
