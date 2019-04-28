<?php

namespace App\Form;

use App\Entity\Guild;
use App\Entity\GuildTag;
use App\Entity\GuildActivity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\HttpFoundation\RequestStack;

class GuildSettingsFormType extends AbstractType
{

  protected $requestStack;

  public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

  private $displayChoices = [
    'Hide' => 'hide',
    'Members' => 'members',
    'Logged in' => 'logged',
    'Public' => 'public'
  ];

  private $activitiesChoices = [
    'PvE' => 'pve',
    'PvP' => 'pvp',
    'WvW' => 'wvw',
    'Raids' => 'raids',
    'Dungeons' => 'dungeons',
    'Fractals' => 'fractals',
    'Guild missions' => 'missions',
    'World boss' => 'wb',
    'Exploration' => 'explo',
    'Jumping puzzles' => 'jump'
  ];

  private $tagsChoices = [
    'Family' => 'family',
    'Casual' => 'casual',
    'Hardcore' => 'hardcore',
    'Role Playing' => 'rp',
    'Support' => 'support',
    'Beginner' => 'beginner',
    'Expert' => 'expert',
    'Adult' => 'adult',
    'Multigaming' => 'multi',
  ];

  public function buildForm(FormBuilderInterface $builder, array $options)
  {

    $request = $this->requestStack->getCurrentRequest();

    $builder
      ->add('description', TextType::class, [
        'required' => false,
        'attr' => [
          'maxlength' => 255
        ]
      ])
      ->add('display_in_directory', CheckboxType::class)
      ->add('guild_activities', EntityType::class, [
        'class' => GuildActivity::class,
        'choice_label' => $request->getLocale(),
        'choice_value' => 'uid',
        'multiple' => true,
        'expanded' => false,
        'attr' => [
          'class' => 'ui dropdown'
        ]
      ])
      ->add('guild_tags', EntityType::class, [
        'class' => GuildTag::class,
        'choice_label' => $request->getLocale(),
        'choice_value' => 'uid',
        'multiple' => true,
        'expanded' => false,
        'attr' => [
          'class' => 'ui dropdown'
        ]
      ])
      ->add('website', UrlType::class, ['required' => false])
      ->add('facebook', UrlType::class, ['required' => false, 'attr' => ['placeholder' => 'Facebook']])
      ->add('twitter', UrlType::class, ['required' => false, 'attr' => ['placeholder' => 'Twitter']])
      ->add('youtube', UrlType::class, ['required' => false, 'attr' => ['placeholder' => 'YouTube']])
      ->add('twitch', UrlType::class, ['required' => false, 'attr' => ['placeholder' => 'Twitch']])
      ->add('discord', UrlType::class, ['required' => false, 'attr' => ['placeholder' => 'Discord']])
      ->add('display_stash', ChoiceType::class, [
        'choices' => $this->displayChoices,
        'attr' => [
          'class' => 'dropdown'
        ]
      ])
      ->add('display_treasury', ChoiceType::class, [
        'choices' => $this->displayChoices,
        'attr' => [
          'class' => 'dropdown'
        ]
      ])
      ->add('display_members', ChoiceType::class, [
        'choices' => $this->displayChoices,
        'attr' => [
          'class' => 'dropdown'
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
