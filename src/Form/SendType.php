<?php

namespace App\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', TextType::class, [
                'label'=>'Number',
                'attr' =>[
                    'placeholder'=>'Number',
                ],
            ])

        ->add('sender', TextType::class, [
        'label'=>'Sender',
        'attr' =>[
            'placeholder'=>'Sender',
        ],
    ])

            ->add('message', TextType::class, [
                'label'=>'Message',
                'attr' =>[
                    'placeholder'=>'Message',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label'=>'Send',
                'attr' =>[
                    'class'=>'btn btn-primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
