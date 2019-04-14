<?php

namespace App\Form;

use App\Entity\Guild;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GuildSettingsFormType extends AbstractType
{

  private $displayChoices = [
    'Hide' => 'hide',
    'Members' => 'members',
    'Logged in' => 'logged',
    'Public' => 'public'
  ];

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('description', TextType::class, [
        'label' => 'Slogan',
        'required' => false,
        'attr' => [
          'maxlength' => 255
        ]
      ])
      ->add('display_in_directory', ChoiceType::class, [
        'choices' => [
          'Yes' => true,
          'No' => false
        ]
      ])
      ->add('display_stash', ChoiceType::class, [
        'choices' => $this->displayChoices
      ])
      ->add('display_treasury', ChoiceType::class, [
        'choices' => $this->displayChoices
      ])
      ->add('display_members', ChoiceType::class, [
        'choices' => $this->displayChoices
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
