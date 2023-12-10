<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssignationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $users  = $options['users'];
        $builder
        ->add('users',EntityType::class,[
            'class'=>Users::class,
            'label'=>false,
            'required'=>true,
            'multiple'=>false,
            'choices'=> $users,
            

        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'users'=>[]
        ]);
    }
}
