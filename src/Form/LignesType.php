<?php

namespace App\Form;

use App\Entity\Lignes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LignesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle_alle')
            ->add('libelle_retour')
            ->add('numero')
            ->add('cout')
            ->add('distance')
            ->add('dureeMoyenneDepart')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lignes::class,
        ]);
    }
}
