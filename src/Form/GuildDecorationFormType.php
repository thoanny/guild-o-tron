<?php

namespace App\Form;

use App\Entity\GuildDecoration;
use App\Entity\Decoration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\RequestStack;

class GuildDecorationFormType extends AbstractType
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
            ->add('quantity', IntegerType::class, [
              'data' => 1
            ])
            ->add('decoration', EntityType::class, [
              'class' => Decoration::class,
              'choice_label' => $request->getLocale(),
              'choice_value' => 'id',
              'multiple' => false,
              'expanded' => false,
              'attr' => [
                'class' => 'ui dropdown'
              ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GuildDecoration::class,
        ]);
    }
}
