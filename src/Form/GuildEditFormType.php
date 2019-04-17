<?php

namespace App\Form;

use App\Entity\Guild;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class GuildEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('introduction')
            ->add('chart')
            ->add('notary', IntegerType::class, [
              'attr' => [
                'min' => 0,
                'max' => 7,
                'step' => 1
              ]
            ])
            ->add('tavern', IntegerType::class, [
              'attr' => [
                'min' => 0,
                'max' => 26,
                'step' => 1
              ]
            ])
            ->add('mine', IntegerType::class, [
              'attr' => [
                'min' => 0,
                'max' => 15,
                'step' => 1
              ]
            ])
            ->add('workshop', IntegerType::class, [
              'attr' => [
                'min' => 0,
                'max' => 33,
                'step' => 1
              ]
            ])
            ->add('market', IntegerType::class, [
              'attr' => [
                'min' => 0,
                'max' => 23,
                'step' => 1
              ]
            ])
            ->add('arena', IntegerType::class, [
              'attr' => [
                'min' => 0,
                'max' => 24,
                'step' => 1
              ]
            ])
            ->add('war_room', IntegerType::class, [
              'attr' => [
                'min' => 0,
                'max' => 45,
                'step' => 1
              ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Guild::class,
        ]);
    }
}
