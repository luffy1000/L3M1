<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('created_at',DateTimeType::class,[
            'input'=>'datetime_immutable',
            'label'=>false,
            'date_widget'=>'single_text',
            
        ])

        ->add('payment', ChoiceType::class,[
            'choices'=>[
                'Payer par Paypal'=>'paypal ',
                'Carte Bancaire'=>'stripe',
            ],
            'label'=>false,
            'required'=>true,
            'multiple'=>false,
            'expanded'=>true,
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
