<?php

namespace App\Form;

use App\Entity\GuildTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminGuildTagEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('uid')
          ->add('fr')
          ->add('en')
          ->add('de')
          ->add('es')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GuildTag::class,
        ]);
    }
}
