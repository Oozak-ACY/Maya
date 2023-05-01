<?php

namespace App\Form;

use App\Entity\RacesAnimaux;
use App\Entity\Animaux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AnimauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('date_naissance')
            ->add('race', EntityType::class, [
                'label' => 'Race',
                'class' => RacesAnimaux::class,
                'choice_label' => 'libelle',
                'multiple' => false,
                'expanded' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animaux::class,
        ]);
    }
}
