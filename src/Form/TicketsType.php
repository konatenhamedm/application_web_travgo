<?php

namespace App\Form;

use App\Entity\Clients;
use App\Entity\Dossier;
use App\Entity\Tickets;
use App\Entity\Voyages;
use Doctrine\ORM\EntityRepository;
use http\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           /* ->add('dateHeure', DateTimeType::Class, [
                "label" => "Date '",
                "required" => false,
                "widget" => 'single_text',
                "input_format" => 'H:i',
                "by_reference" => true,
                "empty_data" => ''
            ])*/
           /* ->add('client', EntityType::class, [
                'class' => Clients::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'numeroCarteAbonne',

            ])*/
            ->add('voyages', EntityType::class, [
                'class' => Voyages::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->andWhere("d.heureArrivee is  null")
                        ->orderBy('d.id', 'DESC');
                },
                'choice_label' => function ($voyage) {
                    return $voyage->info();
                },
                'placeholder' => 'Selectionnez le voyage',
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
