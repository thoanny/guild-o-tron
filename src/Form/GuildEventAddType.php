<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GuildEventAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('start_at', DateTimeType::class, [
              'attr' => [
                'class' => 'datetimepicker'
              ],
              'minutes' => [0, 15, 30, 45],
              'years' => [ date('Y'), date('Y')+1 ]
            ])
            ->add('duration', ChoiceType::class, [
              'choices' => [
                '1 hour' => 1,
                '2 hours' => 2,
                '3 hours' => 3,
                '4 hours' => 4
              ],
              'expanded' => true
            ])
            ->add('group_size', ChoiceType::class, [
              'choices' => [
                'Groupe (5)' => 5,
                'Little squad (10)' => 10,
                'Squad (50)' => 50,
                'Multi-squad (50+)' => 99
              ],
              'expanded' => true
            ])
            ->add('type', ChoiceType::class, [
              'choices' => [
                'Guild event' => 'intra',
                // @todo : Guild allience event (inter)
                'Public event' => 'public'
              ],
              'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
