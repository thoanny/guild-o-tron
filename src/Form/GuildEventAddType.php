<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\GuildActivity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\RequestStack;

class GuildEventAddType extends AbstractType
{

    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $request = $this->requestStack->getCurrentRequest();

        $builder
            ->add('name')
            ->add('activity', EntityType::class, [
              'class' => GuildActivity::class,
              'choice_label' => $request->getLocale(),
              'choice_value' => 'uid',
              'multiple' => false,
              'expanded' => false,
              'attr' => [
                'class' => 'ui dropdown'
              ],
              'required' => false
            ])
            ->add('description')
            ->add('start_at', DateTimeType::class, [
              'attr' => [
                'class' => 'datetimepicker'
              ],
              'minutes' => [0, 15, 30, 45],
              'years' => [ date('Y'), date('Y')+1 ],
              'attr' => ['class' => 'dropdown']
            ])
            ->add('duration', ChoiceType::class, [
              'choices' => [
                '1 hour' => 1,
                '2 hours' => 2,
                '3 hours' => 3,
                '4 hours' => 4
              ],
              'attr' => ['class' => 'dropdown']
            ])
            ->add('group_size', ChoiceType::class, [
              'choices' => [
                'Groupe (5)' => 5,
                'Little squad (10)' => 10,
                'Squad (50)' => 50,
                'Multi-squad (50+)' => 99
              ],
              'attr' => ['class' => 'dropdown']
            ])
            ->add('type', ChoiceType::class, [
              'choices' => [
                'Guild event' => 'intra',
                // @todo : Guild allience event (inter)
                'Public event' => 'public'
              ],
              'attr' => ['class' => 'dropdown']
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
