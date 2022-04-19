<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bio')
            ->add('birth_date', DateType::class, [
                'attr' => ['class' => 'js-datepicker'],
            ]) 
            ->add('email')
            ->add('full_name')
            ->add('phone_number')
            
            ->add('username');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
