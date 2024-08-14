<?php

namespace App\Form;

use App\Entity\UserActiv;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class UserActivType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projectName', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Project Name']
            ])
            ->add('owner', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Owner']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserActiv::class,
        ]);
    }
}
