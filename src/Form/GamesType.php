<?php

namespace App\Form;

use App\Entity\Games;
use phpDocumentor\Reflection\Type;
use Symfony\Component\DependencyInjection\TypedReference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GamesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mots', SearchType::class,[
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'entrez un ou plusieurs mots'
                ]
            ])
            ->add('Rechercher', SubmitType::class)
            ->add('name')
            ->add('description')
            ->add('photo_url',FileType::class,[
                'required'=>false,
                'mapped'=>false,
            ])
            

         ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Games::class,
        ]);
    }
}
