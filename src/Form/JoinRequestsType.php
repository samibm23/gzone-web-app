<?php

namespace App\Form;

use App\Entity\JoinRequests;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JoinRequestsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')
            ->add('requestDate')
            ->add('accepted')
            ->add('responseDate')
            ->add('invitation')
            ->add('user')
            ->add('tournament')
            ->add('team')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JoinRequests::class,
        ]);
    }
}
